<li class="tab-options">
    <div class="btn-group roll-nav roll-right">
        <button class="dropdown tabClose" data-toggle="dropdown">
            {{ $trans['oprations'] }}<i class="fa fa-caret-down" style="padding-left: 3px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" style="min-width: 128px;">
            <li><a class="tabReload" href="javascript:;" onclick="refreshTab();">{{ $trans['refresh_current'] }}</a></li>
            <li><a class="tabCloseCurrent" href="javascript:;" onclick="closeCurrentTab();">{{ $trans['close_current'] }}</a></li>
            <li><a class="tabCloseAll" href="javascript:;" onclick="closeOtherTabs(true);">{{ $trans['close_all'] }}</a></li>
            <li><a class="tabCloseOther" href="javascript:;" onclick="closeOtherTabs();">{{ $trans['close_other'] }}</a></li>
            <li><a class="tabscrollLeft" href="javascript:;" onclick="scrollTabLeft();">{{ $trans['scroll_left'] }}</a></li>
            <li><a class="tabscrollRight" href="javascript:;" onclick="scrollTabRight();">{{ $trans['scroll_right'] }}</a></li>
        </ul>
    </div>
</li>