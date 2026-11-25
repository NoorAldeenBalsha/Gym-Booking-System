<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
    <style>
        :where(html) :is(html,body,div,span,p,a,h1,h2,h3,h4,h5,h6,label,input,select,option,optgroup,textarea,button,form,fieldset,legend,ul,ol,li,dl,dt,dd,figure,figcaption,section,article,header,footer,nav,main,aside,strong,b,em,i,small,sub,sup,blockquote,pre,code,picture,progress,output,summary,details,address,abbr,mark), :is(html, body) {
        all: revert;
        }
        :where(html) input::-webkit-datetime-edit,
        :where(html) input::-webkit-datetime-edit-fields-wrapper,
        :where(html) input::-webkit-datetime-edit-text,
        :where(html) input::-webkit-datetime-edit-month-field,
        :where(html) input::-webkit-datetime-edit-day-field,
        :where(html) input::-webkit-datetime-edit-year-field {
        all: revert;
        }
        :where(html) :is(img, svg) {
        vertical-align: revert;
        }
        :where(html) :is(img, table, thead, tbody, tfoot, tr, th, td, caption, hr, iframe, video) {
        box-sizing: revert;
        }
        :where(html) img {
        border-style: revert;
        }
        :where(html) table {
        border-collapse: revert;
        caption-side: revert;
        }
        :where(html) caption {
        padding-top: revert;
        padding-bottom: revert;
        color: revert;
        text-align: revert;
        }
        :where(html) th {
        text-align: revert;
        }
        :where(html) :is(thead, tbody, tfoot, tr, td, th) {
        border-color: revert;
        border-style: revert;
        border-width: revert;
        }
        :where(html) hr {
        margin: revert;
        color: revert;
        background-color: revert;
        border: revert;
        opacity: revert;
        height: revert;
        overflow: revert;
        }

        :is(*, html) {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        }

        @font-face {
        font-family: 'Montserrat';
        font-style: normal;
        font-weight: 700;
        src: url(../fonts/montserrat-700-latin-ext.woff2) format('woff2');
        unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        font-display: swap;
        }

        @font-face {
        font-family: 'Montserrat';
        font-style: normal;
        font-weight: 700;
        src: url(../fonts/montserrat-700-latin.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        font-display: swap;
        }

        @font-face {
        font-family: 'Montserrat';
        font-style: normal;
        font-weight: 900;
        src: url(../fonts/montserrat-900-latin-ext.woff2) format('woff2');
        unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        font-display: swap;
        }

        @font-face {
        font-family: 'Montserrat';
        font-style: normal;
        font-weight: 900;
        src: url(../fonts/montserrat-900-latin.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        font-display: swap;
        }

        .cl-icon {
        height: 1em;
        width: auto;
        fill: currentColor;
        display: inline-block;
        }

        body {
        padding: 0;
        margin: 0;
        }

        #notfound {
        position: relative;
        height: 100vh;
        background: #030005;
        }

        #notfound .notfound {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        }

        .notfound {
        max-width: 767px;
        width: 100%;
        line-height: 1.4;
        text-align: center;
        }

        .notfound .notfound-404 {
        position: relative;
        height: 180px;
        margin-bottom: 20px;
        z-index: -1;
        }

        .notfound .notfound-404 h1 {
        font-family: 'Montserrat', sans-serif;
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50% , -50%);
        -ms-transform: translate(-50% , -50%);
        transform: translate(-50% , -50%);
        font-size: 224px;
        font-weight: 900;
        margin-top: 0px;
        margin-bottom: 0px;
        margin-left: -12px;
        color: #030005;
        text-transform: uppercase;
        text-shadow: -1px -1px 0px #8400ff, 1px 1px 0px #ff005a;
        letter-spacing: -20px;
        }

        .notfound .notfound-404 h2 {
        font-family: 'Montserrat', sans-serif;
        position: absolute;
        left: 0;
        right: 0;
        top: 110px;
        font-size: 42px;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        text-shadow: 0px 2px 0px #8400ff;
        letter-spacing: 13px;
        margin: 0;
        }

        .notfound a {
        font-family: 'Montserrat', sans-serif;
        display: inline-block;
        text-transform: uppercase;
        color: #ff005a;
        text-decoration: none;
        border: 2px solid;
        background: transparent;
        padding: 10px 40px;
        font-size: 14px;
        font-weight: 700;
        -webkit-transition: 0.2s all;
        transition: 0.2s all;
        }

        .notfound a:hover {
        color: #8400ff;
        }

        @media only screen and (max-width: 767px) {
        .notfound .notfound-404 h2 {
            font-size: 24px;
        }
        }

        @media only screen and (max-width: 480px) {
        .notfound .notfound-404 h1 {
            font-size: 182px;
        }
        }

    </style>

</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h1>404</h1>
				<h2>Page not found</h2>
			</div>
			<a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 shadow-sm">
                Back to Home
            </a>
		</div>
	</div>

</body>

</html>

