<?php

namespace app\api\modules\v1\controllers;

use app\api\modules\v1\models\Banner;
use app\api\modules\v1\models\BannerCategories;
use app\api\modules\v1\models\Category;
use app\api\modules\v1\models\Program;
use app\api\modules\v1\models\BannerMedia;
use app\api\modules\v1\models\Media;
use app\api\modules\v1\models\RetailerBanners;
use app\api\modules\v1\models\Retailer;
use app\lib\helpers\FileHelper;
use app\lib\api\Controller;
use yii\base\Module;
use Affiliate\Affiliate;
use yii\helpers\Json;

class HotDealsController extends Controller
{
    const EXPERIENCE_PROTOCOL = 'https://';
    const EXPERIENCE_BASE_URL = 'experienceoz.com.au/en';
    /**
     * set Model Class if has Model
     *
     * @var string
     */
    public $modelClass = '';

    /**
     * @var bool
     */
    public $isAuthenticate = false;

    /**
     * @var string
     */
    public $type  = 'experience';

    /**
     * @var $url
     */
    public $url;

    /**
     * @var $model
     */
    public $model;

    /**
     * @var $program
     */
    public $program;

    /**
     * HotDealsController constructor.
     * @param string $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);

        if (!$this->isAuthenticate) {

            $this->program = Program::findOne(Program::SPORTSPASS);

            $options = [
                'api_url'  => $this->program['api_url'],
                'username' => app()->params['experience_username'],
                'password' => app()->params['experience_password']
            ];

            $model       = new Affiliate($this->type, $options);
            $this->model = $model->getModel();

            $this->isAuthenticate = true;
        }
    }

    /**
     * @param $data
     * @param $name
     * @return string
     */
    public function saveImageFile($data, $name)
    {
        $image_url = null;

        if (@$file = file_get_contents($data))
        {
            file_put_contents(app()->params['uploadUrl'].'/'.$name, $file);
            chmod(app()->params['uploadUrl'].'/'.$name, 0775);

            $image_url = app()->params['uploadUrl'].'/'.$name;

        }

        return $image_url;
    }

    /**
     * @param $canonicalSegment
     * @param $urlSegment
     */
    public function setUrl($canonicalSegment, $urlSegment)
    {
        $this->url = self::EXPERIENCE_PROTOCOL.$this->program['api_url'].'/'.self::EXPERIENCE_BASE_URL.'/'.$canonicalSegment.'/'.$urlSegment;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sync Experience Oz Hot Deals to our DB
     */
    public function actionSyncHotDeals()
    {
        $data = Json::decode($this->model->hotDeals(), true);

        if ($data)
        {
            foreach ($data['operators'] as $key => $value)
            {
                $model = Banner::findExperienceBannerOrCreate($value['id']);

                if ($model)
                {

                    $model->name = $value['publicName'];
                    $model->type = $value['hotDealMessage'];

                    $this->setUrl($value['canonicalRegionUrlSegment'], $value['urlSegment']);

                    $model->url = $this->getUrl();
                    $model->end_date = $value['hotDealExpiryDate'];

                    $model->configs = Json::encode($value);
                    $model->experience_id = $value['id'];

                    $model->save();

                    if ($value['images'])
                    {
                        $s3 = app()->get('s3');

                        foreach ($value['images'] as $v)
                        {
                            $fileExt  = ($v) ? FileHelper::getFileType($v) : null;
                            $fileName = FileHelper::generateFileName();
                            $media    = ($v) ? $this->saveImageFile($v, $fileName . '.' . $fileExt['ext']) : null;

                            /**
                             * Upload Banner Image to S3
                             * Bucket Sportspass
                             * @return Object
                             */
                            $s3Link = ($media) ? $s3->upload('Staging/banners/media/'. $fileName . '.' . $fileExt['ext'], $media) : null;

                            /**
                             * Remove Banner in local file
                             * @remove image
                             */
                            if ($media)
                                unlink($media);

                            $m = new Media;
                            $m->link = ($s3Link) ? $s3Link['ObjectURL'] : null;;
                            $m->save();

                            $bm = new BannerMedia;

                            $bm->banner_id = $model->id;
                            $bm->media_id  = $m->id;
                            $bm->save();

                            $bc = new BannerCategories;

                            $bc->banner_id = $model->id;
                            $bc->category_id = Category::CATEGORY_EXPERIENCES;

                            $bc->save();


                            $br = new RetailerBanners;
                            $br->banner_id = $model->id;
                            $br->retailer_id = Retailer::RETAILER_EXPERIENCE_OZ;

                            $br->save();

                        }
                    }
                }
            }
        }
    }

}