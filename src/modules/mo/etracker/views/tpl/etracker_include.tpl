[{assign var=mo_etracker__vars value=$oViewConf->mo_etracker__getVars()}]
[{assign var=mo_etracker__noscriptVars value=$oViewConf->mo_etracker__getNoscriptVars($mo_etracker__vars)}]
[{assign var=mo_etracker__config value=$oViewConf->mo_etracker__getConfiguration()}]
[{assign var=mo_etracker__version value=$oViewConf->mo_etracker__getModuleVersion()}]
<script id="_etLoader" type="text/javascript" charset="UTF-8" data-block-cookies="true" data-respect-dnt="true" data-secure-code="[{$mo_etracker__config.mo_etracker__securecode}]" data-plugin-version="[{$mo_etracker__version}]" src="https://static.etracker.com/code/e.js"></script>
<noscript><link rel="stylesheet" media="all" href="https://www.etracker.de/cnt_css.php?et=[{$mo_etracker__config.mo_etracker__securecode}]&amp;v=4.0&amp;java=n&amp;et_easy=0[{$mo_etracker__noscriptVars}]" /></noscript>