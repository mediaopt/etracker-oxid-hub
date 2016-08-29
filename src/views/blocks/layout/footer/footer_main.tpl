[{$smarty.block.parent}]

[{assign var=mo_etracker__config value=$oViewConf->mo_etracker__getConfiguration()}]

[{if $oViewConf->moetIsConfigComplete()}]
    <script type="text/javascript">
        [{if $mo_etracker__config.moetdebug}]
        etCommerce.debugMode = true;
        [{/if}]
        var etCommercePrepareEvents = [];
        [{$oViewConf->mo_etracker__getEventCalls()}].forEach(function(event) {
            etCommercePrepareEvents.push(event);
        });
    </script>
[{/if}]

[{assign var=mo_etracker__user value=$oView->getUser()}]
[{if $mo_etracker__config.moetdebug && $mo_etracker__user && $mo_etracker__user->inGroup('oxidadmin')}]
    <style>
        #moetdebug {
            position: fixed;
            bottom: 0px;
            right: 0px;
            width: 400px;
            background-color: #FFE7DF;
            border-top: 1px solid black;
            border-left: 1px solid black;
            padding: 10px 10px 20px 10px;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            opacity: 0.8;
            filter: alpha(opacity=80);
            text-align: left;
        }

        #moetdebug #moetdebugContent {
            font-size: 15px;
            font-family: monospace;
            line-height: 16px;
        }
    </style>
    <div id="moetdebug">
        <h2>Etracker-Module Debug</h2>
        <div id="moetdebugContent">
            [{foreach from=$oViewConf->moetGetEtrackerVars() key=varName item=value}]
            var [{$varName}] = '[{$value}]';
            [{/foreach}]

            var cc_attributes = new Object();
            cc_attributes["language"] = ["[{$oViewConf->moetGetLanguageAbbr()}]", false];
        </div>
    </div>
    <script type="text/javascript">
        //<![CDATA[
        moetContent = unescape(document.getElementById('moetdebugContent').innerHTML);
        //add breaks => IE "forgets" <pre>-behavior after unescape()
        moetContent = moetContent.replace(/';/g, "';<br />");
        document.getElementById('moetdebugContent').innerHTML = moetContent;
        //]]>
    </script>
[{/if}]

