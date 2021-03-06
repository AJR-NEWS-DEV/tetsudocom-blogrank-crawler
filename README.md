# tetsudocom-blogrank-crawler

## Summary / 概要
鉄道コムにログインし、鉄道ブログランキングをJSON形式のファイルで同じディレクトリに出力します。  
Log in to tetsudo.com and get blog ranking data. This program will create an JSON file.

このプログラムは[Apache License 2.0](LICENSE)の定める範囲でご自由にご利用いただけます。  
必ずライセンスをお読みいただき、ぜひご利用ください。

## Usage / 使用方法
1. [getTetsudoComRank.php](getTetsudoComRank.php)を開き、お使いの鉄道コムへのログイン情報を入力してください。
    - `'YOUR_NICK_NAME'` … ニックネームを入力
    - `'YOUR_PASSWORD'` … パスワードを入力
2. PHPを配置して、Cronジョブ・Google Apps Scriptなどでこのファイルに定期的にHTTP GETリクエストを送信してください。
 
## JSON Structure / JSONの構造
```JSON
{
    "count": {
        "in": 0,
        "out": 0
    },
    "rank": [
        {
            "cat": "総合",
            "url": null,
            "rank": 4
        },
        {
            "cat": "地方交通",
            "url": "/blog/rank/category/%E5%9C%B0%E6%96%B9%E4%BA%A4%E9%80%9A/",
            "rank": 2
        }
    ],
    "info": {
        "exec_date": "2021-02-26T15:02:25+00:00"
    }
}
```
- `"count"` … そのブログのINカウント・OUTカウントです(int)。
- `"rank"` … ランキングの項目は一つずつ区切って生成されます(array)。
  - `"cat"` … カテゴリ名(string)。
  - `"url"` … `https://www.tetsudo.com`の後に続く相対パスを表示します(string | null)。
  - `"rank"` … そのカテゴリの順位を表示します(int)。
- `"exec_date"` … 実行した日時をATOM形式で出力します(string)。
