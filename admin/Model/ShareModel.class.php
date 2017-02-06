<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * share数据库模型.
 */
class ShareModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;

    // 状态map
    public static $STATUS_MAP = array(
        '0' => '禁用',
        '1' => '激活',
    );
    // 定义积分等级
    const JF_GRADE1 = 1;
    const JF_GRADE2 = 2;
    const JF_GRADE3 = 3;

    public static $JF_GRADE_MAP = array(
        '1' => '一级积分',
        '2' => '二级积分',
        '3' => '三级积分'
    );
    // 表名
    public $tableName = 'u_share';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 添加数据验证规则
    public function rules()
    {
        // 验证规则
        return array(
        );

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        $data['ctime'] = time();
        $data['status'] = self::STATUS_APPROVED;
        return $data;

    }

    /**
     * 分享URL生成规则 share_param = uid_rkey_rank.
     * @usege url = ShareModel::model()->gShareUrl(youruid, Jf_rulesModel::JF_SHARE, 'product/productDetail', array('pid' => 1));
     *
     * @param int    $uid     用户id.
     * @param int    $rkey    积分规则key.
     * @param string $baseUrl 分享url.
     * @param array  $bundle  额外信息.
     *
     * @return void
     */
    public function gShareUrl($uid, $rkey, $baseUrl = '', $bundle = array())
    {
        // 解析分享信息
        $shareParamBundle = $this->parseShareParam();
        if (!$shareParamBundle) :
            // uid 组合模式
            $uidConbine = $uid . '#' . '0' . '#' . '0';
        else :
            // 判断用户id
            $uidBundle = explode('#', $shareParamBundle['uid']);
            if (!in_array($uid, $uidBundle)) :
                array_pop($uidBundle);
                array_unshift($uidBundle, $uid);
            endif;
            $uidConbine = implode('#', $uidBundle);
        endif;

        $shareParam = $uidConbine . '_' . $rkey;

        // hash 加密
        $shareParam = arComp('hash.mcrypt')->encrypt($shareParam);

        $bundle['share_param'] = $shareParam;

        return arU($baseUrl, $bundle, 'FULL');


    }

    // 解析分享传回数组
    public function parseShareParam()
    {
        $param = arGet('share_param');

        if (empty($param)) :
            return false;
        else :
            $param = arComp('hash.mcrypt')->decrypt($param);
            if (empty($param)) :
                return false;
            else :
                $arr = explode('_', $param);

                if (count($arr) === 2) :
                    return array('uid' => $arr[0], 'rkey' => $arr[1]);
                else :
                    return false;
                endif;
            endif;
        endif;

    }

}
