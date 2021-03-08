<?php

$firstName = isset($_GET['firstName']) ? $_GET['firstName'] : false;
$lastName = isset($_GET['lastName']) ? $_GET['lastName'] : false;
$role = isset($_GET['role']) ? $_GET['role'] : false;
$tel = isset($_GET['tel']) ? $_GET['tel'] : false;
function stripAccents($string){
    return str_replace(
        array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì',
            'í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú',
            'û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç','È','É',
            'Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö',
            'Ù','Ú','Û','Ü','Ý'),
        array('a','a','a','a','a','a','c','e','e','e','e','i',
            'i','i','i','n','o','o','o','o','o','o','u','u',
            'u','u','y','y','A','A','A','A','A','C','E','E',
            'E','E','I','I','I','I','N','O','O','O','O','O',
            'U','U','U','U','Y'),
        $string
    );
}
$email = strtolower(stripAccents($firstName));

function sanitize_output($buffer) {

    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/', // Remove HTML comments
        '/\>(\s)\</'        // Remove whitespaces between tags
    );

    $replace = array(
        '>',
        '<',
        '\\1',
        '',
        '><'
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}

ob_start("sanitize_output");
?>
<table cellpadding="0" cellspacing="0" style="font-size: medium; font-family: Tahoma;">
    <tbody>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="font-size: medium; font-family: Tahoma;">
                    <tbody>
                    <tr>
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="font-size: medium; font-family: Tahoma;">
                                <tbody>
                                <tr>
                                    <td height="10" style="font-size:10px;line-height:10px"></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="https://www.dismoi.io" style="display: inline-block;padding:0px;" title="DisMoi">
                                            <img role="presentation" src="https://www.dismoi.io/signatures/logos/dismoi.png" style="max-width: 120px; display: inline-block;" width="120" alt="DisMoi">
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="12" style="font-size:12px;line-height:12px"></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;">
                                        <table cellpadding="0" cellspacing="0" style="font-size: 8px; font-family: Tahoma; display: inline-block;">
                                            <tbody>
                                            <tr style="text-align: center;">
                                                <td>
                                                    <a href="https://www.facebook.com/Leseclaireursduweb/" style="display: inline-block;padding:0px;" title="Facebook">
                                                        <img alt="Facebook" src="https://www.dismoi.io/signatures/pictos/facebook.png" style="display:inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://twitter.com/DisMoiCompagnon" style="display:inline-block;padding:0px;" title="Twitter">
                                                        <img alt="Twitter" src="https://www.dismoi.io/signatures/pictos/twitter.png" style="display:inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://www.linkedin.com/company/dismoi/" style="display: inline-block; padding: 0px;" title="LinkedIn">
                                                        <img alt="LinkedIn" src="https://www.dismoi.io/signatures/pictos/linkedin.png" style="display:inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://github.com/dis-moi" style="display:inline-block;padding:0px;" title="GitHub">
                                                        <img alt="Mastodon" src="https://www.dismoi.io/signatures/pictos/github.png" style="display:inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://fosstodon.org/web/accounts/315733" style="display:inline-block;padding:0px;" title="Mastodon">
                                                        <img alt="Mastodon" src="https://www.dismoi.io/signatures/pictos/mastodon.png" style="display:inline-block;" width="20">
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20" style="font-size:20px;line-height:20px"></td>
                                                <td height="20" style="font-size:20px;line-height:20px"></td>
                                                <td height="20" style="font-size:20px;line-height:20px"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <br />
                                        <table cellpadding="0" cellspacing="0" style="font-size:9px;font-family: Tahoma;display:inline-block;background:#dbdbdb;padding:4px 5px;border-radius:16px;">
                                            <tbody>
                                            <tr style="text-align: center;">
                                                <td>
                                                    <a href="https://www.dismoi.io/sources/60/Amazon-Antidote" style="display: inline-block; padding: 0px;" title="Amazon Antidote">
                                                        <img alt="Amazon Antidote" src="https://www.dismoi.io/signatures/pictos/amazon-antidote.png" style="display: inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://www.dismoi.io/sources/16/Le-Meme-en-Mieux" style="display: inline-block; padding: 0px;" title="Le Même en Mieux">
                                                        <img alt="Le Même en Mieux" src="https://www.dismoi.io/signatures/pictos/lmem.png" style="display: inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://www.dismoi.io/sources/25/Le-Meme-En-Local/" style="display: inline-block; padding: 0px;" title="Le Même en Local">
                                                        <img alt="Le Même en Local" src="https://www.dismoi.io/signatures/pictos/lmel.png" style="display: inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://www.dismoi.io/sources/24/Arnaque-Blocker" style="display: inline-block; padding: 0px;" title="Arnaque Blocker">
                                                        <img alt="Arnaque Blocker" src="https://www.dismoi.io/signatures/pictos/arnaque-blocker.png" style="display: inline-block;" width="20">
                                                    </a>
                                                </td>
                                                <td width="4">
                                                    <div></div>
                                                </td>
                                                <td>
                                                    <a href="https://www.dismoi.io/sources/55/CaptainFact.io" style="display: inline-block; padding: 0px;" title="CaptainFact.io">
                                                        <img alt="CaptainFact.io" src="https://www.dismoi.io/signatures/pictos/captain-fact.png" style="display: inline-block;" width="20">
                                                    </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="30">
                            <div></div>
                        </td>
                        <td style="padding: 0px; vertical-align: middle;">
                            <h3 style="margin: 0px; font-size: 16px; line-height: 22px; color: #2754a0;"><?php if($firstName) echo htmlentities(ucwords($firstName)); ?><?php if($firstName && $lastName) echo '&nbsp;'; ?><?php if($lastName) echo htmlentities(ucwords($lastName)); ?></h3>
                            <p style="margin: 0px; color: #3e3e3e; font-size: 12px; line-height: 18px;"
                            ><?= $role; ?> &#x2013; DisMoi</p>
                            <table cellpadding="0" cellspacing="0" style="font-size: medium; font-family: Tahoma; width: 100%;">
                                <tbody>
                                <tr>
                                    <td height="5" style="font-size:5px;line-height:5px"></td>
                                </tr>
                                <tr>
                                    <td direction="horizontal" height="1" style="font-size:1px;line-height:1px;width:100%;border-bottom:1px solid #2754a0;border-left:medium none;display:block;"></td>
                                </tr>
                                <tr>
                                    <td height="5" style="font-size:5px;line-height:5px"></td>
                                </tr>
                                </tbody>
                            </table>
                            <table cellpadding="0" cellspacing="0" style="font-size: medium; font-family: Tahoma;">
                                <tbody>
                                <?php if($tel): ?>
                                <tr height="23" style="vertical-align: middle;">
                                    <td style="vertical-align: middle;" width="25">
                                        <table cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td style="vertical-align: bottom;">
                                                    <img src="http://files.arza-studio.com/dismoi/pictos/phone.png" style="display: inline-block;" width="20">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="padding: 0px; color: #2754a0;">
                                        <a href="tel:+33630747023" style="text-decoration: none; color: #2754a0; font-size: 12px;"><?= $tel; ?></a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr height="23" style="vertical-align: middle;">
                                    <td style="vertical-align: middle;" width="25">
                                        <table cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td style="vertical-align: bottom;">
                                                        <img src="https://www.dismoi.io/signatures/pictos/email.png" style="display: inline-block;" width="20">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="padding: 0px; color: #2754a0;">
                                        <a href="mailto:<?= $email; ?>@dismoi.io" style="text-decoration: none; color: #2754a0; font-size: 12px;"><?= $email; ?>@dismoi.io</a>
                                    </td>
                                </tr>
                                <tr height="23" style="vertical-align: middle;">
                                    <td style="vertical-align: middle;" width="25">
                                        <table cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td style="vertical-align: bottom;">
                                                        <img src="https://www.dismoi.io/signatures/pictos/website.png" style="display: inline-block;" width="20">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="padding: 0px; color: #2754a0;">
                                        <a href="https://www.dismoi.io/" style="text-decoration: none; color: #2754a0; font-size: 12px;">www.dismoi.io</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>