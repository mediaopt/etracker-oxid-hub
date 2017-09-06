[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{assign var="edit" value=$oView->mo_etracker__getEditObject()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSslSelfLink()}]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="mo_etracker__category">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSslSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="mo_etracker__category">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">

    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td valign="top" class="edittext">
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td valign="bottom" height="25px" class="edittext" width="250" nowrap="">
                            [{oxmultilang ident="MO_ETRACKER__CATEGORY"}]
                        </td>
                        <td valign="bottom" height="25px" class="edittext">
                            <input type="text" class="editinput" name="editval[mo_etracker__name]"
                                   value="[{$edit->oxcategories__mo_etracker__name->value}]"/>
                            [{oxinputhelp ident="MO_ETRACKER__CATEGORY_HELP"}]
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="edittext">
                            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="MO_ETRACKER__SAVE"}]" />
                        </td>
                        <td valign="top" class="edittext" align="left" valign="top"></td>
                    </tr>
                </table>
            </td>
        <tr>
    </table>
</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]