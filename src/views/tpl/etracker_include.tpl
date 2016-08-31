[{assign var=moetrackerVars value=$oViewConf->moetGetEtrackerVars()}]
[{assign var=noscriptParameters value=$oViewConf->moetGetNoscriptVars($moetrackerVars)}]
[{assign var=mo_etracker__config value=$oViewConf->mo_etracker__getConfiguration()}]
<script id="_etLoader" type="text/javascript" charset="UTF-8" data-secure-code="[{$mo_etracker__config.moetsecurecode}]" src="//static.etracker.com/code/e.js"></script>
<noscript><link rel="stylesheet" media="all" href="//www.etracker.de/cnt_css.php?et=[{$mo_etracker__config.moetsecurecode}]&amp;v=4.0&amp;java=n&amp;et_easy=0[{$noscriptParameters}]" /></noscript>