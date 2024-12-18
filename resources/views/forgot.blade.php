<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <title>meowgun</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0 "/>
    <meta name="format-detection" content="telephone=no"/>
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,700,700i,900,900i" rel="stylesheet"/>
    <!--<![endif]-->
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
            -webkit-font-smoothing: antialiased !important;
        }

        img {
            border: 0 !important;
            outline: none !important;
        }

        p {
            Margin: 0px !important;
            Padding: 0px !important;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0px;
            mso-table-rspace: 0px;
        }

        td, a, span {
            border-collapse: collapse;
            mso-line-height-rule: exactly;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        .em_blue a {
            text-decoration: none;
            color: #264780;
        }

        .em_grey a {
            text-decoration: none;
            color: #434343;
        }

        .em_white a {
            text-decoration: none;
            color: #ffffff;
        }

        @media only screen and (min-width: 481px) and (max-width: 649px) {
            .em_main_table {
                width: 100% !important;
            }

            .em_wrapper {
                width: 100% !important;
            }

            .em_hide {
                display: none !important;
            }

            .em_aside10 {
                padding: 0px 10px !important;
            }

            .em_h20 {
                height: 20px !important;
                font-size: 1px !important;
                line-height: 1px !important;
            }

            .em_h10 {
                height: 10px !important;
                font-size: 1px !important;
                line-height: 1px !important;
            }

            .em_aside5 {
                padding: 0px 10px !important;
            }

            .em_ptop2 {
                padding-top: 8px !important;
            }
        }

        @media only screen and (min-width: 375px) and (max-width: 480px) {
            .em_main_table {
                width: 100% !important;
            }

            .em_wrapper {
                width: 100% !important;
            }

            .em_hide {
                display: none !important;
            }

            .em_aside10 {
                padding: 0px 10px !important;
            }

            .em_aside5 {
                padding: 0px 8px !important;
            }

            .em_h20 {
                height: 20px !important;
                font-size: 1px !important;
                line-height: 1px !important;
            }

            .em_h10 {
                height: 10px !important;
                font-size: 1px !important;
                line-height: 1px !important;
            }

            .em_font_11 {
                font-size: 12px !important;
            }

            .em_font_22 {
                font-size: 22px !important;
                line-height: 25px !important;
            }

            .em_w5 {
                width: 7px !important;
            }

            .em_w150 {
                width: 150px !important;
                height: auto !important;
            }

            .em_ptop2 {
                padding-top: 8px !important;
            }

            u + .em_body .em_full_wrap {
                width: 100% !important;
                width: 100vw !important;
            }
        }

        @media only screen and (max-width: 374px) {
            .em_main_table {
                width: 100% !important;
            }

            .em_wrapper {
                width: 100% !important;
            }

            .em_hide {
                display: none !important;
            }

            .em_aside10 {
                padding: 0px 10px !important;
            }

            .em_aside5 {
                padding: 0px 8px !important;
            }

            .em_h20 {
                height: 20px !important;
                font-size: 1px !important;
                line-height: 1px !important;
            }

            .em_h10 {
                height: 10px !important;
                font-size: 1px !important;
                line-height: 1px !important;
            }

            .em_font_11 {
                font-size: 11px !important;
            }

            .em_font_22 {
                font-size: 22px !important;
                line-height: 25px !important;
            }

            .em_w5 {
                width: 5px !important;
            }

            .em_w150 {
                width: 150px !important;
                height: auto !important;
            }

            .em_ptop2 {
                padding-top: 8px !important;
            }

            u + .em_body .em_full_wrap {
                width: 100% !important;
                width: 100vw !important;
            }
        }
    </style>
</head>

@php
    $verificationPath = url('/');
        if (isset($userDetails)) {
            $user_id = Illuminate\Support\Facades\Crypt::encryptString($userDetails->id);
            $verificationPath = url("/").'/reset-password/'.$user_id;
        }
@endphp


<body class="em_body" style="margin:0px auto; padding:0px;" bgcolor="#efefef">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center" bgcolor="#efefef">
    <tr>
        <td align="center" valign="top">
            <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table"
                   style="width:650px; table-layout:fixed;">
                <tr>
                    <td align="center" valign="top" style="padding:0 25px;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="25" style="height:25px;" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top"><a href="#" target="_blank"
                                                                   style="text-decoration:none;"></a></td>
                            </tr>
                            <tr>
                                <td height="28" style="height:28px;" class="em_h20">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center" bgcolor="#efefef">
    <tr>
        <td align="center" valign="top" class="em_aside5">
            <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table"
                   style="width:650px; table-layout:fixed;">
                <tr>
                    <td align="center" valign="top" style="padding:0 25px; background-color:#ffffff;"
                        class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="14" style="height:14px; font-size:0px; line-height:0px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="left" valign="top"
                                    style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                                    Hello,
                                </td>
                            </tr>
                            <tr>
                                <td height="20" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="left" valign="top"
                                    style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                                    Forgot your password?
                                </td>
                            </tr>
                            <tr>
                                <td height="20" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="left" valign="top"
                                    style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                                    We received your request to reset your Account password. To confirm
                                    your request and reset your password, please click on the button below:
                                </td>
                            </tr>

                            <tr>
                                <td height="26" style="height:26px;" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table width="250" style="width:250px; background-color:#007bff; border-radius:4px;"
                                           border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td class="em_white" height="42" align="center" valign="middle"
                                                style="font-family: Arial, sans-serif; font-size: 16px; color:#ffffff; font-weight:bold; height:42px;">
                                                <a href="{{ $verificationPath }}" target="_blank"
                                                   style="text-decoration:none; color:#ffffff; line-height:42px; display:block;">Reset
                                                    Password</a></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>


                            <tr>
                                <td height="26" style="height:26px;" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="left" valign="top"
                                    style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                                    If you don't want to reset your password, please ignore this message.
                                </td>
                            </tr>
                            <tr>
                                <td height="26" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="left" valign="top"
                                    style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                                    DO NOT FORWARD this email. The verify link is private.
                                </td>
                            </tr>
                            <tr>
                                <td height="26" class="em_h20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="em_grey" align="left" valign="top"
                                    style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                                    Thanks & Regards,<br/>

                                </td>
                            </tr>
                            <tr>
                                <td height="4" style="height:4px;" class="em_h20">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center" bgcolor="#efefef">
    <tr>
        <td align="center" valign="top">
            <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table"
                   style="width:650px; table-layout:fixed;">
                <tr>
                    <td align="center" valign="top" style="padding:0 25px;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td width="12" align="left" valign="middle"
                                                style="font-size:0px; line-height:0px; width:12px;">
                                                {{--                                                <a href="#"--}}
                                                {{--                                                                                                       target="_blank"--}}
                                                {{--                                                                                                       style="text-decoration:none;"><img--}}
                                                {{--                                                        src="/assets/pilot/images/templates/img_1.png" width="12"--}}
                                                {{--                                                        height="16" alt="" border="0"--}}
                                                {{--                                                        style="display:block; max-width:12px;"/></a>--}}
                                            </td>
                                            <td width="7" style="width:7px; font-size:0px; line-height:0px;"
                                                class="em_w5">&nbsp;
                                            </td>
                                            <td class="em_grey em_font_11" align="left" valign="middle"
                                                style="font-family: Arial, sans-serif; font-size: 13px; line-height: 15px; color:#434343;">
                                                <a href="#" target="_blank"
                                                   style="text-decoration:none; color:#434343;"></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="35" style="height:35px;" class="em_h20">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="1" bgcolor="#dadada" style="font-size:0px; line-height:0px; height:1px;"><img
                                src="/assets/pilot/images/templates/spacer.gif" width="1" height="1" alt="" border="0"
                                style="display:block;"/></td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="padding:0 25px;" class="em_aside10">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td height="16" style="font-size:0px; line-height:0px; height:16px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellspacing="0" cellpadding="0" align="center" class="em_wrapper">
                                        <tr>
                                            <td class="em_grey" align="center" valign="middle"
                                                style="font-family: Arial, sans-serif; font-size: 11px; line-height: 16px; color:#434343;">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="16" style="font-size:0px; line-height:0px; height:16px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="em_hide" style="line-height:1px;min-width:650px;background-color:#efefef;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
