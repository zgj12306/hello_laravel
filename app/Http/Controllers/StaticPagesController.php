<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaticPagesController extends Controller
{
    public function home()
    {
        $feed_items = [];
        if (Auth::check()) {
            $feed_items = Auth::user()->feed()->paginate(30);
        }
        return view('static_pages/home', compact('feed_items'));
    }

    public function help()
    {
        return view('static_pages/help');
    }

    public function about()
    {
        return view('static_pages/about');
    }

    /**
     * 实现无重复字符的字符串所有排列组合
     * @param $str
     */
    public function a($str)
    {
        $count = strlen($str);
        $arr = str_split($str);
        $tempArr = [$arr[0]];
        for ($i = 1; $i < $count; $i++) {
            $tempArr = $this->t($tempArr, $arr[$i]);
        }
        var_dump(count($tempArr));
    }

    public function t($tempArr, $char)
    {
        $temp = [];
        $len = strlen($tempArr[0]);
        foreach ($tempArr as $val) {
            for ($i = 0; $i <= $len; $i++) {
                $temp[] = substr_replace($val, $char, $i, 0);
            }
        }
        return $temp;
    }

    public function arrange($str)
    {
        $a = str_split($str);
        $temp = [];
        $this->perm($a, 0, count($a) - 1, $temp);
        var_dump(count($temp));
    }

    public function perm(&$ar, $begin, $end, &$temp)
    {
        if ($begin == $end) {
            $temp[] = join('', $ar);
            return;
        } else {
            for ($i = $begin; $i <= $end; $i++) {
                $this->swap($ar[$begin], $ar[$i]);
                $this->perm($ar, $begin + 1, $end, $temp);
                $this->swap($ar[$begin], $ar[$i]);
            }
        }
    }

    public function swap(&$a, &$b)
    {
        $c = $a;
        $a = $b;
        $b = $c;
    }
}
