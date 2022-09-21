<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 30px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            a {
                text-decoration: none;
            }

            table {
                border-collapse: collapse;
                border: solid 1px #ccc;
            }
            table td, table th {
                padding: 10px;
                border: solid 1px #ccc;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    Result of crawling <a href="https://agencyanalytics.com">agencyanalytics.com</a>
                </div>

                <h2>Statistics</h2>
                <table>
                    <tr>
                        <th>Number of pages crawled</th>
                        <td>{{ $number_of_pages }}</td>
                    </tr>
                    <tr>
                        <th>Number of a unique images</th>
                        <td>{{ $number_of_images }}</td>
                    </tr>
                    <tr>
                        <th>Number of unique internal links</th>
                        <td>{{ $number_of_internal_links }}</td>
                    </tr>
                    <tr>
                        <th>Number of unique external links</th>
                        <td>{{ $number_of_external_links }}</td>
                    </tr>
                    <tr>
                        <th>Average page load in seconds</th>
                        <td>{{ round($avg_page_load, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Average word count</th>
                        <td>{{ round($avg_word_count, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Average title length</th>
                        <td>{{ round($avg_title_length, 2) }}</td>
                    </tr>
                </table>

                <h2>Crawled pages</h2>
                <table>
                    <tr>
                        <th>URL</th>
                        <th>HTTP Status</th>
                    </tr>
                    @foreach ($pages as $url => $page)
                    <tr>
                        <td><a href="https://agencyanalytics.com{{ $url }}">{{ $url }}</a></td>
                        <td>{{ $page["status"] }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </body>
</html>
