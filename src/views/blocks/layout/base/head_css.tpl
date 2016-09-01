[{$smarty.block.parent}]

[{assign var=moetrackerVars value=$oViewConf->moetGetEtrackerVars()}]
[{assign var=noscriptParameters value=$oViewConf->moetGetNoscriptVars($moetrackerVars)}]
[{assign var=mo_etracker__config value=$oViewConf->mo_etracker__getConfiguration()}]

[{capture name="moetracker"}]
    [{foreach from=$moetrackerVars key=varName item=value}]
        var [{$varName}] = '[{$value}]';
    [{/foreach}]

    var cc_attributes = new Object();
    cc_attributes["language"] = ["[{$oViewConf->moetGetLanguageAbbr()}]", false];
[{/capture}]

[{if $oViewConf->moetIsConfigComplete()}]
    <!-- Copyright (c) 2000-2016 etracker GmbH. All rights reserved. -->
    <!-- This material may not be reproduced, displayed, modified or distributed -->
    <!-- without the express prior written permission of the copyright holder. -->
    <!-- etracker tracklet 4.0 -->
    <script type="text/javascript">
        [{$smarty.capture.moetracker}]
        //var et_pagename = "";
        //var et_areas = "";
        //var et_url = "";
        //var et_target = "";
        //var et_ilevel = 0;
        //var et_tval = "";
        //var et_cust = 0;
        //var et_tonr = "";
        //var et_tsale = 0;
        //var et_basket = "";
        //var et_lpage = "";
        //var et_trig = "";
        //var et_sub = "";
        //var et_se = "";
        //var et_tag = "";
    </script>
    [{include file="mo_etracker__etracker_include.tpl"}]
    <!-- etracker tracklet 4.0 end -->
[{/if}]