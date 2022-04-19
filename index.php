<?php
header("Content-Type: text/html;charset=utf-8");
require("../../../class.phpmailer.php");
require("../../../rands.php");
require("../../../timer.php");
include_once("../../../sql.php");
$responddata = array("code"=>"","message"=>"");
$address = $_POST['useremail'];
$userip = $_POST['ip'];

if ($address == "" || $userip == "") {
    $responddata['code'] = "";
    $responddata['message'] = "年轻人不要太好奇，请注意你的行为！";
    info($responddata);
    mysqli_close($conn);
    exit;
}else if ($conn->connect_error) {
    $responddata['code'] = "";
    $responddata['message'] = "服务器配置错误";
    info($responddata);
    mysqli_close($conn);
    exit;
}

$c = new rands();
$randomNum = $c -> random();
$randomNumberOnly = $c -> randomOnlyNumber();

$t = new timer();
$overdueTime = $t -> getOverdueTimer();
$IntervalTimer = $t -> getIntervalTimer();
$NowTimer = $t -> getNowTimer();

$result = mysqli_query($conn,"SELECT * FROM emailrannum WHERE onlyIp = '$userip'");
$isexistence = mysqli_fetch_assoc($result);
if ($isexistence) {
    $sqlIntervaltTime = $isexistence["intervaltimer"];
    $sqlOverdueTime = $isexistence["overduetimer"];
    $sqlOnlyId = $isexistence["onlyID"];
    if (intval($sqlIntervaltTime) >= intval($NowTimer)) {
        $responddata['code'] = "";
        $responddata['message'] = "获取验证码间隔1分钟";
        info($responddata);
    }else if (intval($sqlOverdueTime) >= intval($NowTimer)) {
        mysqli_query($conn,"UPDATE emailrannum SET intervaltimer = '$IntervalTimer' WHERE onlyID = '$sqlOnlyId'");
        $m = new email();
        if (!$m -> yx($address,$isexistence["emailtempid"])) {
            $responddata['code'] = "";
            $responddata['message'] = "请检查邮箱是否正确";
            info($responddata);
        }else {
            $responddata['code'] = $sqlOnlyId;
            $responddata['message'] = "验证码发送成功";
            info($responddata);
        }
    }else {
        mysqli_query($conn,"UPDATE emailrannum SET intervaltimer = '$IntervalTimer' WHERE onlyID = '$sqlOnlyId'");
        mysqli_query($conn,"UPDATE emailrannum SET overduetimer = '$overdueTime' WHERE onlyID = '$sqlOnlyId'");
        mysqli_query($conn,"UPDATE emailrannum SET emailtempid = '$randomNum' WHERE onlyID = '$sqlOnlyId'");
        $m = new email();
        if (!$m -> yx($address,$randomNum)) {
            mysqli_query($conn,"DELETE FROM emailrannum WHERE onlyID = '$sqlOnlyId'");
            $responddata['code'] = "";
            $responddata['message'] = "请检查邮箱是否正确";
            info($responddata);
        }else {
            $responddata['code'] = $sqlOnlyId;
            $responddata['message'] = "验证码发送成功";
            info($responddata);
        }
    }
}else {
    $sql = "INSERT INTO emailrannum (onlyID, emailtempid, overduetimer, onlyIp, intervaltimer) VALUES ('$randomNumberOnly', '$randomNum', '$overdueTime', '$userip', '$IntervalTimer')";
    if ($conn -> query($sql) === TRUE) {
        $m = new email();
        if (!$m -> yx($address,$randomNum)) {
            mysqli_query($conn,"DELETE FROM emailrannum WHERE onlyID = '$randomNumberOnly'");
            $responddata['code'] = "";
            $responddata['message'] = "请检查邮箱是否正确"; 
            info($responddata);
        }else {
            $responddata['code'] = $randomNumberOnly;
            $responddata['message'] = "验证码发送成功";
            info($responddata);
        }
    } else {
        $responddata['code'] = "";
        $responddata['message'] = "配置有误";
        info($responddata);
    }
}
class email {
    function yx($useremail,$number) {
        $state = true;
        $mail = new PHPMailer();
        $mail -> IsSMTP();
        $mail -> Host = "smtp.163.com";
        $mail -> SMTPAuth = true;
        $mail -> Username = "daytoosmile@163.com";
        $mail -> Password = "GGCDMQWWBTFBWYZD";
        $mail -> Port = 25;
        $mail -> From = "daytoosmile@163.com";
        $mail -> FromName = "拾光管理员";
        $mail -> AddAddress("$useremail", "拾光用户");
        $mail -> Subject = "拾光验证码";
        $mail -> Body = "您的注册验证码：" . $number . "，本次验证码有效时间5分钟。（为了您的账号安全，请勿泄漏你的验证码！）";
        if (!$mail->Send()) {
            $state = false;
        }
        return $state;
    }
}
function info($e) {
    echo json_encode($e);
}
mysqli_close($conn);
exit;
?>
