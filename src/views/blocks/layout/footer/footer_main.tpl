[{$smarty.block.parent}]

[{include file="mo_etracker__eventhandler.tpl"}]

[{assign var=mo_etracker__config value=$oViewConf->mo_etracker__getConfiguration()}]
[{assign var=mo_etracker__user value=$oView->getUser()}]
[{if $mo_etracker__config.mo_etracker__debug && $mo_etracker__user && $mo_etracker__user->inGroup('oxidadmin')}]
    <style>
        #mo_etracker__debug {
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

        #mo_etracker__debug #mo_etracker__debugContent {
            font-size: 15px;
            font-family: monospace;
            line-height: 16px;
        }
    </style>
    <div id="mo_etracker__debug">
        <h2>Etracker-Module Debug</h2>
        <div id="mo_etracker__debugContent">
            [{foreach from=$oViewConf->mo_etracker__getVars() key=varName item=value}]
            var [{$varName}] = '[{$value}]';
            [{/foreach}]

            var cc_attributes = new Object();
            cc_attributes["language"] = ["[{$oViewConf->mo_etracker__getLanguageAbbr()}]", false];
        </div>
    </div>
    <script type="text/javascript">
        //<![CDATA[
        mo_etracker__debugContent = unescape(document.getElementById('mo_etracker__debugContent').innerHTML);
        //add breaks => IE "forgets" <pre>-behavior after unescape()
        mo_etracker__debugContent = mo_etracker__debugContent.replace(/';/g, "';<br />");
        document.getElementById('mo_etracker__debugContent').innerHTML = mo_etracker__debugContent;
        //]]>
    </script>
[{/if}]

