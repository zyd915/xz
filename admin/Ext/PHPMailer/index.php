<?php
require("class.phpmailer.php"); //下载的文件必须放在该文件所在目录
$mail = new PHPMailer(); //建立邮件发送类
$mail->CharSet = "UTF-8";
$address ="1072626447@qq.com";
$mail->IsSMTP(); // 使用SMTP方式发送
$mail->Host = "smtp.163.com"; // 您的企业邮局域名
$mail->SMTPAuth = true; // 启用SMTP验证功能
$mail->Username = "13688327275@163.com"; // 邮局用户名(请填写完整的email地址)
$mail->Password = "665690"; // 邮局密码
$mail->Port=25;
$mail->From = "13688327275@163.com"; //邮件发送者email地址
$mail->FromName = "13688327275@163.com";
$mail->AddAddress("$address", "云海");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
//$mail->AddReplyTo("", "");

//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
//$mail->AddAttachment("d:/aa.txt"); // 添加附件
//$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式

$mail->Subject = "我就发个邮件啊"; //邮件标题
$mail->Body="hello,world";
$mail->Body = "我是条垃圾邮件！oo"; //邮件内容
//$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略

if(!$mail->Send())
{
echo "邮件发送失败. <p>";
echo "错误原因: " . $mail->ErrorInfo;
exit;
}

echo "邮件发送成功";
?>