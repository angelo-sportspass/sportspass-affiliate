<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\db\Command;
use yii\db\Expression;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * This Command Seeds Data To Role Table and User Table.
 * @table All Roles
 * @table Admin User
 * @author Angelo Gabisan <ag@cashrg.com.au>
 * @since 2.0
 */
class SeedController extends Controller
{
    const NAME = 'name';

    const ROLE_ADMIN = 1;
    const ROLE_SUB_ADMIN = 2;
    const ROLE_OTHER = 3;
    const ROLE_CLIENT = 4;
    const ROLE_DATA_MANAGEMENT = 5;
    const ROLE_VA = 6;
    const ROLE_REPORTS = 7;

    protected $roles = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SUB_ADMIN => 'Sub Admin',
        self::ROLE_OTHER => 'Other',
        self::ROLE_CLIENT => 'Client',
        self::ROLE_DATA_MANAGEMENT => 'Data Management',
        self::ROLE_VA => 'Virtual Assistant',
        self::ROLE_REPORTS => 'Reports'
    ];

    protected $user = [

    ];

    /**
     * @param $sql
     * @param array $params
     * @return Command
     */
    protected function sql($sql = null, $params = [])
    {
        return Yii::$app->db->createCommand($sql, $params);
    }

    /**
     * This command stores data to Role & User Tables
     * @command php yii seed [default command to seed role table]
     * @command php yii seed user [command to seed user table admin : admin]
     * @param null $table
     * @throws \Exception
     */
    public function actionIndex($table = null)
    {
        $start = microtime(true);
        if (!$table) {

            $this->stdout("Seeding Role Table!..\n");

            foreach ($this->roles as $key => $value) {

                $role = [
                    self::NAME => $value
                ];

                $this->sql()->insert('role', $role)->execute();

                $this->stdout("Inserting $value role...");
                $this->stdout("\n");
                sleep(0.5);
            }

            $time = microtime(true) - $start;
            $this->stdout("\n");

            $this->stdout("Done in " . sprintf('%.3f', $time) . " secs...!\n");

        } else if ($table == 'user') {

            //@todo create user Seed..
            $this->stdout("Seeding " . $table . " Table.....");

            $user = [
                'auth_key' => Yii::$app->getSecurity()->generateRandomString(32),
                'email' => 'admin@email.com',
                'user_name' => 'admin',
                'first_name' => 'admin',
                'last_name' => 'admin',
                'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
                'status' => true,
                'created_at' => new Expression('now()')
            ];

            $this->sql()->insert('user', $user)->execute();

            $this->stdout("Inserting Admin user...");
            $this->stdout("\n");
            $this->stdout("Username : admin");
            $this->stdout("\n");
            $this->stdout("Password : admin");
            $this->stdout("\n");

            $time = microtime(true) - $start;

            $this->stdout("Done in " . sprintf('%.3f', $time) . " secs...!\n");

        } else if ($table == 'account') {

            $account = [
                'user_name' => 'admin',
                'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
                'first_name' => 'admin',
                'last_name' => 'admin',
                'email' => 'test@email.com',
                'dob' => new Expression('NOW()'),
                'status' => 1,
                'created_at' => new Expression('NOW()')
            ];

            $this->sql()->insert('account', $account)->execute();

        } else if ($table == 'program') {

        } else {

            $this->stdout("\n");
            throw new \Exception("Could find table $table.");

        }
    }
}