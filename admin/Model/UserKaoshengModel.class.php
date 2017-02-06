<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * user 数据库模型.
 */
class UserKaoshengModel extends ArModel
{
    public static $GRADE_MAP = array(
        '高一', '高二', '高三'
    );

    // 表名
    public $tableName = 'u_user_kaosheng';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    // 这个是用于格式化添加想法赞的方法
    public function formatData($data)
    {
        return $data;

    }

    // 获取考生信息
    public function getKaoshengDetail($users)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($users)) :
            foreach ($users as &$user) :
                $user = $this->getKaoshengDetail($user);
            endforeach;
        else :
            $user = $users;

            $kaosheng = UserKaoshengModel::model()
                ->getDb()
                ->where(array('uid' => $user['uid']))
                ->queryRow();

            $user['kaosheng'] = $kaosheng;
            // 购卡信息
            $user['vip'] = UserVcardModel::model()
                ->getDb()
                ->where(array('uid' => $user['uid'], 'vtype' => UserVcardModel::VTYPE_VIP))
                ->queryRow();

            if (!$user['vip']) :
                $user['normal'] = UserVcardModel::model()
                    ->getDb()
                    ->where(array('uid' => $user['uid'], 'vtype' => UserVcardModel::VTYPE_NORMAL))
                    ->queryRow();
            endif;

            $user['serial'] = UserSerialsModel::model()->getDb()->where(array('uid' => $user['uid']))->queryRow();
            return $user;

        endif;

        return $users;

    }

}
