<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8 />
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui">
    <title>GraphQL Playground</title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/graphql-playground-react/build/static/css/index.css" />
    <link rel="shortcut icon" href="//cdn.jsdelivr.net/npm/graphql-playground-react/build/favicon.png" />
    <script src="//cdn.jsdelivr.net/npm/graphql-playground-react/build/static/js/middleware.js"></script>
</head>
<body>
<style type="text/css">
    html {
        font-family: "Open Sans", sans-serif;
        overflow: hidden;
    }

    body {
        margin: 0;
        background: #6f4ca5;
    }

    .playgroundIn {
        -webkit-animation: playgroundIn 0.5s ease-out forwards;
        animation: playgroundIn 0.5s ease-out forwards;
    }

    @-webkit-keyframes playgroundIn {
        from {
            opacity: 0;
            -webkit-transform: translateY(10);
            -ms-transform: translateY(10);
            transform: translateY(10);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
    }

    @keyframes playgroundIn {
        from {
            opacity: 0;
            -webkit-transform: translateY(10);
            -ms-transform: translateY(10);
            transform: translateY(10);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
    }

    .fadeOut {
        -webkit-animation: fadeOut 2s ease-out forwards;
        animation: fadeOut 2s ease-out forwards;
    }

    @-webkit-keyframes fadeIn {
        from {
            opacity: 0;
            -webkit-transform: translateY(-10);
            -ms-transform: translateY(-10);
            transform: translateY(-10);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            -webkit-transform: translateY(-10);
            -ms-transform: translateY(-10);
            transform: translateY(-10);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
    }

    @-webkit-keyframes fadeOut {
        from {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
        to {
            opacity: 0;
            -webkit-transform: translateY(-10);
            -ms-transform: translateY(-10);
            transform: translateY(-10);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
        to {
            opacity: 0;
            -webkit-transform: translateY(-10);
            -ms-transform: translateY(-10);
            transform: translateY(-10);
        }
    }

    @-webkit-keyframes appearIn {
        from {
            opacity: 0;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
    }

    @keyframes appearIn {
        from {
            opacity: 0;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(0);
            -ms-transform: translateY(0);
            transform: translateY(0);
        }
    }

    @-webkit-keyframes scaleIn {
        from {
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }
        to {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }
    }

    @keyframes scaleIn {
        from {
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }
        to {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }
    }

    @-webkit-keyframes innerDrawIn {
        0% {
            stroke-dashoffset: 70;
        }
        50% {
            stroke-dashoffset: 140;
        }
        100% {
            stroke-dashoffset: 210;
        }
    }

    @keyframes innerDrawIn {
        0% {
            stroke-dashoffset: 70;
        }
        50% {
            stroke-dashoffset: 140;
        }
        100% {
            stroke-dashoffset: 210;
        }
    }

    @-webkit-keyframes outerDrawIn {
        0% {
            stroke-dashoffset: 76;
        }
        100% {
            stroke-dashoffset: 152;
        }
    }

    @keyframes outerDrawIn {
        0% {
            stroke-dashoffset: 76;
        }
        100% {
            stroke-dashoffset: 152;
        }
    }

    .hHWjkv {
        -webkit-transform-origin: 0 0;
        -ms-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.2222222222222222s;
        animation: scaleIn 0.25s linear forwards 0.2222222222222222s;
    }

    .gCDOzd {
        -webkit-transform-origin: 0 0;
        -ms-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.4222222222222222s;
        animation: scaleIn 0.25s linear forwards 0.4222222222222222s;
    }

    .hmCcxi {
        -webkit-transform-origin: 0 0;
        -ms-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.6222222222222222s;
        animation: scaleIn 0.25s linear forwards 0.6222222222222222s;
    }

    .eHamQi {
        -webkit-transform-origin: 0 0;
        -ms-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.8222222222222223s;
        animation: scaleIn 0.25s linear forwards 0.8222222222222223s;
    }

    .byhgGu {
        -webkit-transform-origin: 0 0;
        -ms-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 1.0222222222222221s;
        animation: scaleIn 0.25s linear forwards 1.0222222222222221s;
    }

    .llAKP {
        -webkit-transform-origin: 0 0;
        -ms-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 1.2222222222222223s;
        animation: scaleIn 0.25s linear forwards 1.2222222222222223s;
    }

    .bglIGM {
        -webkit-transform-origin: 64px 28px;
        -ms-transform-origin: 64px 28px;
        transform-origin: 64px 28px;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.2222222222222222s;
        animation: scaleIn 0.25s linear forwards 0.2222222222222222s;
    }

    .ksxRII {
        -webkit-transform-origin: 95.98500061035156px 46.510000228881836px;
        -ms-transform-origin: 95.98500061035156px 46.510000228881836px;
        transform-origin: 95.98500061035156px 46.510000228881836px;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.4222222222222222s;
        animation: scaleIn 0.25s linear forwards 0.4222222222222222s;
    }

    .cWrBmb {
        -webkit-transform-origin: 95.97162628173828px 83.4900016784668px;
        -ms-transform-origin: 95.97162628173828px 83.4900016784668px;
        transform-origin: 95.97162628173828px 83.4900016784668px;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.6222222222222222s;
        animation: scaleIn 0.25s linear forwards 0.6222222222222222s;
    }

    .Wnusb {
        -webkit-transform-origin: 64px 101.97999572753906px;
        -ms-transform-origin: 64px 101.97999572753906px;
        transform-origin: 64px 101.97999572753906px;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 0.8222222222222223s;
        animation: scaleIn 0.25s linear forwards 0.8222222222222223s;
    }

    .bfPqf {
        -webkit-transform-origin: 32.03982162475586px 83.4900016784668px;
        -ms-transform-origin: 32.03982162475586px 83.4900016784668px;
        transform-origin: 32.03982162475586px 83.4900016784668px;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 1.0222222222222221s;
        animation: scaleIn 0.25s linear forwards 1.0222222222222221s;
    }

    .edRCTN {
        -webkit-transform-origin: 32.033552169799805px 46.510000228881836px;
        -ms-transform-origin: 32.033552169799805px 46.510000228881836px;
        transform-origin: 32.033552169799805px 46.510000228881836px;
        -webkit-transform: scale(0);
        -ms-transform: scale(0);
        transform: scale(0);
        -webkit-animation: scaleIn 0.25s linear forwards 1.2222222222222223s;
        animation: scaleIn 0.25s linear forwards 1.2222222222222223s;
    }

    .iEGVWn {
        opacity: 0;
        stroke-dasharray: 76;
        -webkit-animation: outerDrawIn 0.5s ease-out forwards 0.3333333333333333s, appearIn 0.1s ease-out forwards 0.3333333333333333s;
        animation: outerDrawIn 0.5s ease-out forwards 0.3333333333333333s, appearIn 0.1s ease-out forwards 0.3333333333333333s;
        -webkit-animation-iteration-count: 1, 1;
        animation-iteration-count: 1, 1;
    }

    .bsocdx {
        opacity: 0;
        stroke-dasharray: 76;
        -webkit-animation: outerDrawIn 0.5s ease-out forwards 0.5333333333333333s, appearIn 0.1s ease-out forwards 0.5333333333333333s;
        animation: outerDrawIn 0.5s ease-out forwards 0.5333333333333333s, appearIn 0.1s ease-out forwards 0.5333333333333333s;
        -webkit-animation-iteration-count: 1, 1;
        animation-iteration-count: 1, 1;
    }

    .jAZXmP {
        opacity: 0;
        stroke-dasharray: 76;
        -webkit-animation: outerDrawIn 0.5s ease-out forwards 0.7333333333333334s, appearIn 0.1s ease-out forwards 0.7333333333333334s;
        animation: outerDrawIn 0.5s ease-out forwards 0.7333333333333334s, appearIn 0.1s ease-out forwards 0.7333333333333334s;
        -webkit-animation-iteration-count: 1, 1;
        animation-iteration-count: 1, 1;
    }

    .hSeArx {
        opacity: 0;
        stroke-dasharray: 76;
        -webkit-animation: outerDrawIn 0.5s ease-out forwards 0.9333333333333333s, appearIn 0.1s ease-out forwards 0.9333333333333333s;
        animation: outerDrawIn 0.5s ease-out forwards 0.9333333333333333s, appearIn 0.1s ease-out forwards 0.9333333333333333s;
        -webkit-animation-iteration-count: 1, 1;
        animation-iteration-count: 1, 1;
    }

    .bVgqGk {
        opacity: 0;
        stroke-dasharray: 76;
        -webkit-animation: outerDrawIn 0.5s ease-out forwards 1.1333333333333333s, appearIn 0.1s ease-out forwards 1.1333333333333333s;
        animation: outerDrawIn 0.5s ease-out forwards 1.1333333333333333s, appearIn 0.1s ease-out forwards 1.1333333333333333s;
        -webkit-animation-iteration-count: 1, 1;
        animation-iteration-count: 1, 1;
    }

    .hEFqBt {
        opacity: 0;
        stroke-dasharray: 76;
        -webkit-animation: outerDrawIn 0.5s ease-out forwards 1.3333333333333333s, appearIn 0.1s ease-out forwards 1.3333333333333333s;
        animation: outerDrawIn 0.5s ease-out forwards 1.3333333333333333s, appearIn 0.1s ease-out forwards 1.3333333333333333s;
        -webkit-animation-iteration-count: 1, 1;
        animation-iteration-count: 1, 1;
    }

    .dzEKCM {
        opacity: 0;
        stroke-dasharray: 70;
        -webkit-animation: innerDrawIn 1s ease-in-out forwards 1.3666666666666667s, appearIn 0.1s linear forwards 1.3666666666666667s;
        animation: innerDrawIn 1s ease-in-out forwards 1.3666666666666667s, appearIn 0.1s linear forwards 1.3666666666666667s;
        -webkit-animation-iteration-count: infinite, 1;
        animation-iteration-count: infinite, 1;
    }

    .DYnPx {
        opacity: 0;
        stroke-dasharray: 70;
        -webkit-animation: innerDrawIn 1s ease-in-out forwards 1.5333333333333332s, appearIn 0.1s linear forwards 1.5333333333333332s;
        animation: innerDrawIn 1s ease-in-out forwards 1.5333333333333332s, appearIn 0.1s linear forwards 1.5333333333333332s;
        -webkit-animation-iteration-count: infinite, 1;
        animation-iteration-count: infinite, 1;
    }

    .hjPEAQ {
        opacity: 0;
        stroke-dasharray: 70;
        -webkit-animation: innerDrawIn 1s ease-in-out forwards 1.7000000000000002s, appearIn 0.1s linear forwards 1.7000000000000002s;
        animation: innerDrawIn 1s ease-in-out forwards 1.7000000000000002s, appearIn 0.1s linear forwards 1.7000000000000002s;
        -webkit-animation-iteration-count: infinite, 1;
        animation-iteration-count: infinite, 1;
    }

    .hIXxYY {
        background-color: #6f4ca5;
    }

    #loading-wrapper {
        position: absolute;
        width: 100vw;
        height: 100vh;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-align-items: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
    }

    .logo {
        width: 75px;
        height: 75px;
        margin-bottom: 20;
        opacity: 0;
        -webkit-animation: fadeIn 1s ease-out forwards;
        animation: fadeIn 1s ease-out forwards;
    }

    .text {
        font-size: 32px;
        font-weight: 200;
        text-align: center;
        color: rgba(255, 255, 255, 0.6);
        opacity: 0;
        -webkit-animation: fadeIn 1s ease-out forwards;
        animation: fadeIn 1s ease-out forwards;
    }

    .dGfHfc {
        font-weight: 400;
    }
</style>
<div id="loading-wrapper">
    <svg class="logo" xmlns="http://www.w3.org/2000/svg" width="654" height="248" viewBox="0 0 654 248">
        <defs>
            <style>
                .cls-1 {
                    fill: #fff;
                    fill-rule: evenodd;
                }
            </style>
        </defs>
        <path class="cls-1" d="M118.263,197.817h22.145l-39.989-66.864c21.069-6.235,32.249-18.49,32.249-39.559,0-27.3-18.92-41.494-55.469-41.494L40,50V198l16-1V134l28.144,0.178ZM56,120V65l20.984-.481c25.155,0,35.9,8.17,35.9,26.875,0,20.424-12.254,28.379-33.539,28.379Zm212.978,77.817h19.779L241.028,49.9H217.379L169.434,197.817h18.92l11.61-37.839h57.619Zm-64.5-53.1,24.294-79.334,24.08,79.334H204.479ZM351,28l-17,6V223l17-6V28Zm85,21-18,2V198l75.8-.183,4.15-18.34L434,182Zm178.266,0.9L511,50V65h42V199l16-2,1-132h42Z"></path>
        <path class="cls-1" d="M334,24l17-6V0L334,6V24Z"></path>
    </svg>
    <div class="text">
        <span class="dGfHfc">GraphQL Playground</span>
    </div>
</div>
<div id="root"></div>
<script type="text/javascript">
    @php
        /** @var Railt\LaravelProvider\Config\Endpoint $endpoint */
        $endpoint = \array_first($endpoints);
    @endphp

    window.addEventListener('load', function (event) {
        setTimeout(function() {
            const loadingWrapper = document.getElementById('loading-wrapper');
            loadingWrapper.classList.add('fadeOut');

            const root = document.getElementById('root');
            root.classList.add('playgroundIn');


            GraphQLPlayground.init(root, {
                @if($endpoint)
                name: '{{ \ucwords($endpoint->getName()) }}',
                endpoint: '{{ \route($endpoint->getRouteName()) }}',
                @endif
                settings: @json($ui->getSettings()),
                tabs: [
                    @foreach($endpoints as $endpoint)
                    {
                        name: '{{ \ucwords($endpoint->getName()) }}',
                        endpoint: '{{ \route($endpoint->getRouteName()) }}',
                        headers: {
                            'X-XSRF-Token': '{{ \csrf_token() }}'
                        }
                    },
                    @endforeach
                ]
            });
        }, 1000);
    })
</script>
</body>
</html>
