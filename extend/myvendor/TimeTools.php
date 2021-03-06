<?php

namespace myvendor;


class TimeTools
{
    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today()
    {
        return [
            mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            mktime(23, 59, 59, date('m'), date('d'), date('Y'))
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday()
    {
        $yesterday = strtotime("-1 Days");
        return [
            mktime(0, 0, 0, date('m',$yesterday), date('d',$yesterday), date('Y',$yesterday)),
            mktime(23, 59, 59, date('m',$yesterday), date('d',$yesterday), date('Y',$yesterday))
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp))),
            strtotime(date('Y-m-d', strtotime("this week Sunday", $timestamp))) + 24 * 3600 - 1
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp))),
            strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + 24 * 3600 - 1
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month()
    {
        return [
            mktime(0, 0, 0, date('m'), 1, date('Y')),
            mktime(23, 59, 59, date('m'), date('t'), date('Y'))
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth()
    {
        $time = time();
        $lastMonth = strtotime("-1 Months",strtotime(date('Y',$time)."-".date('m',$time)."-01"));
        $begin = mktime(0, 0, 0, date('m',$lastMonth) - 1, 1, date('Y',$lastMonth));
        $end = mktime(23, 59, 59, date('m',$begin), date('t', $begin), date('Y',$begin));

        return [$begin, $end];
    }

    /**
     * 返回最近几个月开始和结束的时间戳
     * @param $num int 月数
     * @return array 开始时间和结束时间
     */
    public static function lastMonths($num)
    {
        $time = time();
        $lastMonth = strtotime("-$num Months",strtotime(date('Y',$time)."-".date('m',$time)."-01"));
        $begin = mktime(0, 0, 0, date('m',$lastMonth) - 1, 1, date('Y',$lastMonth));
        $end = mktime(23, 59, 59, date('m',$time), date('t', $time), date('Y',$time));

        return [$begin, $end];
    }

    /**
     * 返回本季度开始和结束的时间戳
     *
     * @return array
     */
    public static function season()
    {
        $season = ceil((date('n'))/3);//当月是第几季度
        return [
            mktime(0, 0, 0,$season*3-3+1,1,date('Y')),
            mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y'))
        ];
    }

    /**
     * 返回上季度开始和结束的时间戳
     *
     * @return array
     */
    public static function lastSeason()
    {
        $season = ceil((date('n'))/3);//当月是第几季度
        $year = date('Y');
        if($season==1){
            $season = 4;
            $year--;
        }
        return [
            mktime(0, 0, 0,($season-1)*3+1,1,$year),
            mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),$year)
        ];
    }

    /**
     * 返回最近几个季度开始和结束的时间戳
     * @param $num int 月数
     * @return array 开始时间和结束时间
     */
    public static function lastSeasons($num)
    {
        $season = ceil((date('n'))/3);//当月是第几季度
        $year = date('Y')-ceil(($num-$season)/4);
        $season = 4-(($num-$season)%4);
        return [
            mktime(0, 0, 0,($season-1)*3+1,1,$year),
            mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),$year)
        ];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year()
    {
        return [
            mktime(0, 0, 0, 1, 1, date('Y')),
            mktime(23, 59, 59, 12, 31, date('Y'))
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear()
    {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year)
        ];
    }

    /**
     * 返回最近几年开始和结束的时间戳
     * @param $num int 月数
     * @return array 开始时间和结束时间
     */
    public static function lastYears($num)
    {
        $time = time();
        $lastYear = strtotime("-$num Years",strtotime(date('Y',$time)."-".date('m',$time)."-01"));
        $begin = mktime(0, 0, 0, date('m',$lastYear) - 1, 1, date('Y',$lastYear));
        $end = mktime(23, 59, 59, date('m',$time), date('t', $time), date('Y',$time));

        return [$begin, $end];
    }

    public static function dayOf()
    {

    }

    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @param int $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     * @return array
     */
    public static function dayToNow($day = 1, $now = true)
    {
        $end = time();
        if (!$now) {
            list($foo, $end) = self::yesterday();
        }

        return [
            mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
            $end
        ];
    }

    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAgo($day = 1)
    {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }

    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAfter($day = 1)
    {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }

    /**
     * 天数转换成秒数
     *
     * @param int $day
     * @return int
     */
    public static function daysToSecond($day = 1)
    {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $week
     * @return int
     */
    public static function weekToSecond($week = 1)
    {
        return self::daysToSecond() * 7 * $week;
    }

    private static function startTimeToEndTime()
    {

    }
}