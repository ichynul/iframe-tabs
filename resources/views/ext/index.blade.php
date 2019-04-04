@extends('admin::index', ['header' => $header])

@section('content')
<div class="content-tabs">
    <button class="roll-nav roll-left tabLeft" onclick="scrollTabLeft()">
        <i class="fa fa-backward"></i>
    </button>
    <nav class="page-tabs menuTabs tab-ui-menu" id="tab-menu">
        <div class="page-tabs-content" style="margin-left: 0px;">

        </div>
    </nav>
    <button class="roll-nav roll-right tabRight" onclick="scrollTabRight()">
        <i class="fa fa-forward" style="margin-left: 4px;"></i>
    </button>
    <div class="btn-group roll-nav roll-right">
        <button class="dropdown tabClose" data-toggle="dropdown">
            {{ $trans['oprations'] }}<i class="fa fa-caret-down" style="padding-left: 3px;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" style="min-width: 128px;">
            <li><a class="tabReload" href="javascript:refreshTab();">{{ $trans['refresh_current'] }}</a></li>
            <li><a class="tabCloseCurrent" href="javascript:closeCurrentTab();">{{ $trans['close_current'] }}</a></li>
            <li><a class="tabCloseAll" href="javascript:closeOtherTabs(true);">{{ $trans['close_all'] }}</a></li>
            <li><a class="tabCloseOther" href="javascript:closeOtherTabs();">{{ $trans['close_other'] }}</a></li>
        </ul>
    </div>
</div>
<div class="content-iframe">
    <div class="tab-content " id="tab-content">
    </div>
</div>

<script>
    $(function() {
        window.refresh_current = "{{ $trans['refresh_current'] }}";
        window.open_in_new = "{{ $trans['open_in_new'] }}";
        window.open_in_pop = "{{ $trans['open_in_pop'] }}";

        window.use_icon = "{{ $use_icon }}" == '1';
        window.pass_urls = '{{ $pass_urls }}'.split(',');
        window.home_title = '{{ $home_title }}';
        window.home_uri = '{{ $home_uri }}';
        window.home_icon = '{{ $home_icon }}';

        window.openPop = function(url, title) {
            layer.open({
                type: 2,
                title: title,
                anim: 2,
                closeBtn: 1,
                shade: false,
                maxmin: true, //开启最大化最小化按钮
                area: ['90%', '90%'],
                content: url,
            });
        }

        if (!window.layer) {
            window.layer = {
                load: function() {
                    var html = '<div style="z-index:999;margin:0 auto;position:fixed;top:90px;left:50%;" class="loading-message"><img src="/vendor/laravel-admin-ext/iframe-tabs/images/loading-spinner-grey.gif" /></div>';
                    $('.tab-content').append(html);
                    return 1;
                },
                close: function(index) {
                    $('.tab-content .loading-message').remove();
                },
                open: function() {
                    alert('layer.js dose not work.');
                }
            };
        }

        $('body').on('click', '.sidebar-menu li a,.navbar-nav>li a', function() {
            var url = $(this).attr('href');
            if (!url || url == '#') {
                return;
            }
            if (window.pass_urls) {
                for (var i in window.pass_urls) {
                    if (url.indexOf(window.pass_urls[i]) > -1) {
                        return true;
                    }
                }
            }
            var icon = '<i class="fa fa-edge"></i>';
            if ($(this).find('i.fa').size()) {
                var icon = $(this).find('i.fa').prop("outerHTML");
            }
            var span = $(this).find('span');
            addTabs({
                id: url.replace(/\W/g, '_'),
                title: span.size() ? span.text() : $(this).text().length ? $(this).text() : '*',
                close: true,
                url: url,
                urlType: 'absolute',
                icon: icon
            });

            if ($(this).parents('.dropdown').size()) {
                $(this).parents('.dropdown').find('.dropdown-toggle').trigger('click');
            }
            return false;
        });
        if (window == top) {
            var url = window.home_uri;
            addTabs({
                id: '_admin_dashboard',
                title: window.home_title,
                close: false,
                url: url,
                urlType: 'absolute',
                icon: '<i class="fa ' + window.home_icon + '"></i>'
            });
        } else {
            if (/\/admin\/?$/i.test(location.href)) {
                $('body').html('....');
                location.href = window.home_uri;
            }
        }
    });
</script>

@endsection 