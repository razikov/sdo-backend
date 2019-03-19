<?php

namespace app\helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use DOMDocument;

/**
 * Description of XmlHelper
 * Везде проверить индексы!
 *
 * @author aleksey
 */
class XmlHelper
{
    function generate_password($number)
    {
        $arr = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
            'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u',
            'v', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0'
        ];
        $pass = "";
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

    function translit($string)
    {
        $replace = [
            "'" => "", "`" => "", "а" => "a", "А" => "a", "б" => "b", "Б" => "b",
            "в" => "v", "В" => "v", "г" => "g", "Г" => "g", "д" => "d", "Д" => "d",
            "е" => "e", "Е" => "e", "ж" => "zh", "Ж" => "zh", "з" => "z", "З" => "z",
            "и" => "i", "И" => "i", "й" => "y", "Й" => "y", "к" => "k", "К" => "k",
            "л" => "l", "Л" => "l", "м" => "m", "М" => "m", "н" => "n", "Н" => "n",
            "о" => "o", "О" => "o", "п" => "p", "П" => "p", "р" => "r", "Р" => "r",
            "с" => "s", "С" => "s", "т" => "t", "Т" => "t", "у" => "u", "У" => "u",
            "ф" => "f", "Ф" => "f", "х" => "h", "Х" => "h", "ц" => "c", "Ц" => "c",
            "ч" => "ch", "Ч" => "ch", "ш" => "sh", "Ш" => "sh", "щ" => "sch",
            "Щ" => "sch", "ъ" => "", "Ъ" => "", "ы" => "y", "Ы" => "y", "ь" => "",
            "Ь" => "", "э" => "e", "Э" => "e", "ю" => "yu", "Ю" => "yu", "я" => "ya",
            "Я" => "ya", "і" => "i", "І" => "i", "ї" => "yi", "Ї" => "yi",
            "є" => "e", "Є" => "e"
        ];
        return $str = iconv("UTF-8", "ISO-8859-1", strtr($string, $replace));
    }

    function getLogin($fam = '', $name = '', $otch = '')
    {
        $str = $fam;
        $str .= mb_substr($name, 0, 1, 'utf-8');
        $str .= mb_substr($otch, 0, 1, 'utf-8');
        return self::translit($str);
    }

    /**
     * +
     * @param string $fname
     * @return array
     */
    function read($fname)
    {
        $spreadsheet = IOFactory::load($fname);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $nRow = $sheet->getHighestRow();
        $nColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $arr = [];
        for ($i = 1; $i <= $nRow; $i++) {
            for ($j = 1; $j <= $nColumn; $j++) {
                $row[$j] = trim($sheet->getCellByColumnAndRow($j, $i)->getValue());
            }
            if ($row[1] != '') {
                $pass = self::generate_password(6);
                $fio = [];
                foreach (explode(' ', trim($row[1])) as $word) {
                    if ($word != '') {
                        $fio[] = trim($word);
                    }
                }
                $arr[] = [
                    'fam' => $fam = $fio[0],
                    'name' => $name = $fio[1],
                    'otch' => $otch = $fio[2],
                    'email' => trim($row[2]),
                    'phone' => '',
                    'login' => self::getLogin($fam, $name, $otch),
                    'password' => $pass,
                    'password_md5' => md5($pass),
                    'mr' => '', 
                    'ou' => trim($row[3])
                ];
            } else {
                
            }
        }
        return $arr;
    }
    
    function readUserList($fname)
    {
        $spreadsheet = IOFactory::load($fname);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $nRow = $sheet->getHighestRow();
        $nColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $arr = [];
        for ($i = 2; $i <= $nRow; $i++) {
            $login = trim($sheet->getCellByColumnAndRow(3, $i)->getValue());
            $pasword = trim($sheet->getCellByColumnAndRow(4, $i)->getValue());
            $arr[] = [
                'login' => $login,
                'password' => $pasword,
            ];
        }
        return $arr;
    }
    
    /**
     * +
     * @param string $fname
     * @return type
     */
    function read_ilias_users($fname)
    {
        $spreadsheet = IOFactory::load($fname);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $nRow = $sheet->getHighestRow();
        $nColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $arr = [];
        for ($i = 5; $i <= $nRow; $i++) {
            for ($j = 1; $j <= $nColumn; $j++) {
                $row[$j] = trim($sheet->getCellByColumnAndRow($j, $i)->getValue());
            }
            if ($row[1] != '' and $row[2] != '') {
                $fname = explode(' ', trim($row[1]));
                $lname = explode(' ', trim($row[2]));
                foreach ($fname as $item) {
                    if ($item != '') {
                        $arr[$i][] = $item;
                    }
                }
                foreach ($lname as $item) {
                    if ($item != '') {
                        $arr[$i][] = $item;
                    }
                }
            }
        }
        return $arr;
    }

    function renderXML($arr_users, $filename = 'render.xml')
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $nodeUsers = $dom->createElement("Users");
        $users = $dom->appendChild($nodeUsers);
        foreach ($arr_users as $modelUser) {
            if ($modelUser->validate()) {
                $nodeUser = $dom->createElement("User");
                $user = $users->appendChild($nodeUser);
                //Атрибуты
                $Language = $dom->createAttribute('Language');
                $Language->value = 'ru';
                $user->appendChild($Language);
                $Action = $dom->createAttribute('Action');
                //$Action->value = 'Update';//!!!!!!!!!!!!!!!!!!!!!!!!!
                $Action->value = 'Insert';
                $user->appendChild($Action);
                //==============
                $nodeLogin = $dom->createElement("Login");
                $Login = $user->appendChild($nodeLogin);
                $cdata = $dom->createCDATASection($modelUser->login);
                $Login->appendChild($cdata);
                // NOTE: Нельзя создавать пользователя без хотя бы 1 глобальной роли
                foreach ($modelUser->roles as $modelUserRole) {
                    $nodeRole = $dom->createElement("Role");
                    $Role = $user->appendChild($nodeRole);
                    $cdata = $dom->createCDATASection($modelUserRole->objectData->title);
                    $Role->appendChild($cdata);
                    $Id = $dom->createAttribute('Id');
//                    $Id->value = $modelUserRole->iliasId; //'il_0_role_276';
                    $Id->value = $modelUserRole->iliasId;
                    $Role->appendChild($Id);
                    $Type = $dom->createAttribute('Type');
                    $Type->value = $modelUserRole->hasGlobal() ? 'Global' : 'Local';
                    $Role->appendChild($Type);
                }
                //==============
                $nodeActive = $dom->createElement("Active");
                $active = $user->appendChild($nodeActive);
                $cdata = $dom->createCDATASection('true');
                $active->appendChild($cdata);
                //==============
                $nodePassword = $dom->createElement("Password");
                $Password = $user->appendChild($nodePassword);
//                $cdata = $dom->createCDATASection($modelUser->passwd);
                $cdata = $dom->createCDATASection($modelUser->rawPassword);
                $Password->appendChild($cdata);
                //Атрибуты
                $Type = $dom->createAttribute('Type');
//                $Type->value = 'ILIAS3';
                $Type->value = 'PLAIN';
                $Password->appendChild($Type);
                //==============
                $nodeFirstname = $dom->createElement("Firstname");
                $Firstname = $user->appendChild($nodeFirstname);
                $cdata = $dom->createCDATASection($modelUser->firstname);
                $Firstname->appendChild($cdata);
                //==============
                $nodeLastname = $dom->createElement("Lastname");
                $Lastname = $user->appendChild($nodeLastname);
                $cdata = $dom->createCDATASection($modelUser->lastname);
                $Lastname->appendChild($cdata);
                //==============
                //$nodeGender = $dom->createElement("Gender");
                //$gender = $user->appendChild($nodeGender);
                //$cdata = $dom->createCDATASection('n');
                //$gender->appendChild($cdata);
                //==============
                $nodeEmail = $dom->createElement("Email");
                $Email = $user->appendChild($nodeEmail);
                $cdata = $dom->createCDATASection($modelUser->email);
                $Email->appendChild($cdata);
                //==============
                $nodePhone = $dom->createElement("PhoneOffice");
                $Phone = $user->appendChild($nodePhone);
                $cdata = $dom->createCDATASection($modelUser->phone_office);
                $Phone->appendChild($cdata);
                //==============
                $nodePhone = $dom->createElement("Institution");
                $Phone = $user->appendChild($nodePhone);
                $cdata = $dom->createCDATASection($modelUser->institution);
                $Phone->appendChild($cdata);
            }
        }
        $dom->save($filename);
    }

    /**
     * Проверить!
     * @param type $arr_users
     */
    function renderExcel($arrUsers, $fileName = "render.xls")
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Список');

        $headers = ['ФИО', 'email', 'логин', 'пароль', 'МР', 'ОУ'];
        $i = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow(1, $i, $headers[0]);
            $sheet->setCellValueByColumnAndRow(2, $i, $headers[1]);
            $sheet->setCellValueByColumnAndRow(3, $i, $headers[2]);
            $sheet->setCellValueByColumnAndRow(4, $i, $headers[3]);
            $sheet->setCellValueByColumnAndRow(5, $i, $headers[4]);
            $sheet->setCellValueByColumnAndRow(6, $i, $headers[5]);
        }
        $i++;
        foreach ($arrUsers as $user) {
            $sheet->setCellValueByColumnAndRow(1, $i, $user->fullName);
            $sheet->setCellValueByColumnAndRow(2, $i, $user->email);
            if ($user->validate()) {
                $sheet->setCellValueByColumnAndRow(3, $i, $user->login);
                $sheet->setCellValueByColumnAndRow(4, $i, $user->rawPassword);
            }
            $sheet->setCellValueByColumnAndRow(5, $i, '');
            $sheet->setCellValueByColumnAndRow(6, $i, $user->institution);
            $i++;
        }
        
        $writer = IOFactory::createWriter($spreadsheet, "Xls");
        $writer->save($fileName);
    }
    
    function read_ou($fname)
    {
        $spreadsheet = IOFactory::load($fname);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $nRow = $sheet->getHighestRow();
        $nColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());
        
        $arr = [];
        for ($i = 2; $i <= $nRow; $i++) {
            for ($j = 1; $j <= $nColumn; $j++) {
                $row[$j] = trim($sheet->getCellByColumnAndRow($j, $i)->getValue());
            }
            if ($row[0] != '' and $row[1] != '' and $row[3] != '') {
                $code = trim($row[0]);
                $mr = trim($row[1]);
                $name = trim($row[3]);
                if ($mr = validate_mr($mr)) {
                    $arr[] = ['code' => $code, 'mr' => $mr, 'name' => $name];
                }
            } else {
                //категория
                //$cat = $row['1'];
            }
        }
        return $arr;
    }

    function validate_mr($str)
    {
        $mrs = [
            'Большесельский МР',
            'Борисоглебский МР',
            'Брейтовский МР',
            'Гаврилов-Ямский МР',
            'Даниловский МР',
            'Любимский МР',
            'Мышкинский МР',
            'Некоузский МР',
            'Некрасовский МР',
            'Первомайский МР',
            'г. Переславль-Залесский',
            'Переславский МР',
            'Пошехонский МР',
            'Ростовский МР',
            'г. Рыбинск',
            'Рыбинский МР',
            'Тутаевский МР',
            'Угличский МР',
            'г. Ярославль',
            'Ярославский МР'
        ];

        foreach ($mrs as $mr) {
            if (mb_strpos($mr, $str, 0, 'utf-8') !== false) {
                return $mr;
            }
        }
        
        return false;
    }

}
