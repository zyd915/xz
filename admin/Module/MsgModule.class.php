<?php
class MsgModule
{
    // 发送消息
    public function send($uids, $data)
    {
        if (empty($data['content']) || empty($uids)) :
            return false;
        endif;

        $msgBundle = array();
        if (is_array($uids)) :
            foreach ($uids as $uid) :
                $msgBundle[] = array(
                    'touid' => $uid,
                    'sender' => $data['sender'],
                    'stime' => time(),
                    'readed' => MsgModel::READED_NO,
                    'content' => $data['content'],
                    'type' => MsgModel::TYPE_ADMIN,
                );
            endforeach;
        endif;
        $sResult = MsgModel::model()->getDb()->batchInsert($msgBundle);
        if ($sResult) :
            // 记录日志
            LogModel::model()->record(arCfg('admin.name'), '给用户发送消息', '内容：' . $data['content']);
            return true;
        else :
            return false;
        endif;

    }

}
