<?xml version="1.0" encoding="iso-8859-1"?>
<!-- Generator: Adobe Illustrator 15.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="{!! $dimensions['width']/2 !!}px"
     height="{!! $dimensions['height']/2 !!}px" viewBox="0 0 {!! $dimensions['width'] !!} {!! $dimensions['height'] !!}"
     style="enable-background:new 0 0 {!! $dimensions['width'] !!} {!! $dimensions['height'] !!};" xml:space="preserve">

	<style type="text/css">
        <![CDATA[

        @import 'https://fonts.googleapis.com/css?family=Play:400,700';

        @font-face {
            font-family: 'League Gothic';
            src: url("/fonts/league-gothic/leaguegothic-regular-webfont.eot");
            src: url("/fonts/league-gothic/leaguegothic-regular-webfont.eot?#iefix") format("embedded-opentype"),
            url("/fonts/league-gothic/leaguegothic-regular-webfont.woff") format("woff"),
            url("/fonts/league-gothic/leaguegothic-regular-webfont.ttf") format("truetype"),
            url("/fonts/league-gothic/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
            font-weight: normal;
            font-style: normal;
        }

        text {
            font-family: 'League Gothic';
            fill: #fff;
            text-transform: uppercase;
        }

        #stripe {
            mix-blend-mode: soft-light;
            opacity: .7;
        }

        #stripe .grid {
            opacity: 1;
        }

        .team {
            font-size: 58px;
            text-transform: uppercase;
            text-anchor: middle;
            alignment-baseline: middle;
        }

        .score {
            font-size: 153px;
            fill: #575242;
            text-anchor: middle;
        }

        .result--win {
            fill: #92DB00;
        }

        .result--loss {
            fill: #FF291C;
        }

        .result--tie {
            fill: #f29800;
        }

        .badge-title {
            font-family: 'League Gothic';
            font-size: 40px;
            text-transform: uppercase;
            fill: #fff;
        }

        .grid {
            opacity: 0.25;
            fill: url(#grid);
        }

        .player-name {
            font-size: 150px;
        }
            .player-name--large {
                font-size: 195px;
            }
            .name--last {
                fill: #f5d100;
            }

        .stat-text--number,
        .stat-text--number-sub,
        .stat-text--title,
        .stat-text--title-sub {
            text-anchor: middle;
            text-transform: uppercase;
        }

            .stat-text--number {
                font-size: 95px;
            }
                .stat-text--long {
                    font-size: 76px;
                }
                .stat-text--number-sub {
                    font-size: 24px;
                }
                .stat-text--prefix,
                .stat-text--suffix {
                    font-size: 58.5px;
                    baseline-shift: super;
                }

                .stat-text--suffix {
                    font-size: 41px;
                }

            .stat-text--title {
                font-size: 38px;
            }
            .stat-text--title-sub {
                font-size: 25px;
            }


        .update {
            font-size: 95px;
            text-anchor: middle;
            letter-spacing: .01em;
        }
            .update--bigger { font-size: 125px; }
            .mention--yellow { fill: #f5d100; }
            .mention--grey { fill: #cfcfcf; }

            .meta {
                font-family: 'Play';
                font-size: 30px;
                letter-spacing: -.05em;
                fill: #d5d5d5;
                fill-opacity: .6;
                text-transform: none;
                text-anchor: middle;
            }

            .meta-score {
                font-weight: bold;
            }

        ]]>
    </style>

    <defs>
        <pattern id="grid" x="540" y="540" width="72" height="72" patternUnits="userSpaceOnUse" viewBox="32 -72.249 72 72" style="overflow:visible;">
            <g>
                <polygon style="fill:none;" points="32,-0.249 32,-72.25 104,-72.25 104,-0.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="31.75" y1="-63.249" x2="104.25" y2="-63.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="31.75" y1="-27.249" x2="104.25" y2="-27.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="0" y1="-63.249" x2="28" y2="-63.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="31.75" y1="-45.249" x2="104.25" y2="-45.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="31.75" y1="-9.249" x2="104.25" y2="-9.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="0" y1="-45.249" x2="28" y2="-45.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="77" y1="-72.5" x2="77" y2="0.001"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="41" y1="-72.5" x2="41" y2="0.001"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="5" y1="-68.187" x2="5" y2="-40.249"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="95" y1="-72.5" x2="95" y2="0.001"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="59" y1="-72.5" x2="59" y2="0.001"/>
                <line style="fill:none;stroke:#231F20;stroke-width:0.3;" x1="23" y1="-68.187" x2="23" y2="-40.249"/>
            </g>
        </pattern>

        <linearGradient id="blue-trans-bottom" x1="0" y1="0" x2="0" y2="1">
            <stop stop-color="#2f3157" stop-opacity="0" offset="0%"/>
            <stop stop-color="#2f3157" offset="100%"/>
        </linearGradient>

        <linearGradient id="blue-trans-right" x1="0" y1="0" x2="1" y2="0">
            <stop stop-color="#2f3157" stop-opacity="0" offset="0%"/>
            <stop stop-color="#2f3157" offset="100%"/>
        </linearGradient>

        <filter id="shadow">
            <feDropShadow dx="0" dy="2" stdDeviation="5"/>
        </filter>

        <filter id="blur">
            <feGaussianBlur in="SourceGraphic" stdDeviation="5" />
        </filter>

        @yield('defs')
    </defs>

    @yield('content')
</svg>
