[{$smarty.block.parent}]

[{if $oViewConf->mo_etracker__isConfigComplete()}]
    <!-- Copyright (c) 2000-2016 etracker GmbH. All rights reserved. -->
    <!-- This material may not be reproduced, displayed, modified or distributed -->
    <!-- without the express prior written permission of the copyright holder. -->
    <script type="text/javascript">
        [{include file="mo_etracker__etracker_vars.tpl"}]
    </script>
    [{include file="mo_etracker__etracker_include.tpl"}]
[{/if}]