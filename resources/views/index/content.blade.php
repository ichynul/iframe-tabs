@extends('admin::index', ['header' => $header])

@section('content')
<div class="tab-content " id="tab-content">
</div>

<script>

    $(function () {

        //Note! You cannot use both layout-boxed and fixed at the same time. Anything else can be mixed together.
        if(!$('body').hasClass('layout-boxed'))
        {
            $('body').addClass('fixed'); //layout Fixed: use the class .fixed to get a fixed header and sidebar.
        }

        window.refresh_current = "{{ $trans['refresh_current'] }}";
        window.open_in_new = "{{ $trans['open_in_new'] }}";
        window.open_in_pop = "{{ $trans['open_in_pop'] }}";
        window.refresh_succeeded = "{{ $trans['refresh_succeeded'] }}";

        window.use_icon = "{{ $use_icon }}" == '1';
        window.pass_urls = '{{ $pass_urls }}'.split(',');
        window.home_title = '{{ $home_title }}';
        window.home_uri = '{{ $home_uri }}';
        window.home_icon = '{{ $home_icon }}';
        window.iframes_index = '{{ $iframes_index }}';
        window.tabs_left = '{{ $tabs_left }}';
        window.bind_urls = '{{ $bind_urls }}';
        window.bind_selecter = '{{ $bind_selecter }}';

        window.Pops = [];

        window.openPop = function (url, title, area) {
            if (!area) {
                area = [$('#tab-content').width() + 'px', ($('#tab-content').height() - 5) + 'px'];
            }
            
            var index = layer.open({
                content: url,
                type: 2,
                title: title,
                anim: 2,
                closeBtn: 1,
                shade: false,
                maxmin: true, //开启最大化最小化按钮
                area: area,
                //offset: 'rb'
            });
            window.Pops.push(index);

            return index;
        }

        window.openTab = function (url, title, icon, page_id, close, urlType) {
            if (!url) {
                alert('url is empty.');
                return;
            }
            if (icon) {
                if (!/^<i/i.test(icon)) {
                    icon = '<i class="fa ' + icon + '"></i>';
                }
            } else {
                icon = '<i class="fa fa-file-text"></i>';
            }

            addTabs({
                id: page_id || url.replace(/\W/g, '_'),
                title: title || 'New page',
                close: close != false && close != 0,
                url: url,
                urlType: urlType || 'absolute',
                icon: icon
            });
        }

        if (!window.layer) {
            window.layer = {
                load: function () {
                    var html = '<div style="z-index:999;margin:0 auto;position:fixed;top:90px;left:50%;" class="loading-message"><img src="/vendor/laravel-admin-ext/iframe-tabs/images/loading-spinner-grey.gif" /></div>';
                    $('.tab-content').append(html);
                    return 1;
                },
                close: function (index) {
                    $('.tab-content .loading-message').remove();
                },
                open: function () {
                    alert('layer.js dose not work.');
                }
            };
        }

        $('body').on('click', '#tab-menu a.menu_tab', function () {
            var pageId = getPageId(this);
            var $ele = null;
            $(".sidebar-menu li a").each(function () {
                var $meun = $(this);
                if ($meun.attr('data-pageid') == pageId) {
                    $ele = $meun;
                    return false; //退出循环
                }
            });
            if ($ele) {
                $ele.parents('.treeview').not('.active').find('> a').trigger('click');
                setTimeout(function () {
                    var $parent = $ele.parent().addClass('active');
                    $parent.siblings('.treeview.active').removeClass('active');
                    $parent.siblings().removeClass('active').find('li').removeClass('active')
                }, 500);
            }
        });

        $('body').on('click', '.sidebar-menu li a,.navbar-nav>li a,.sidebar .user-panel a,.sidebar-form .dropdown-menu li a', function () {
            if ($(this).hasClass('container-refresh')) {
                var pageId = getActivePageId();

                var iframe = findIframeById(pageId);

                iframe[0].contentWindow.$.admin.reload();

                $.admin.toastr.success(refresh_succeeded, '', { positionClass: "toast-top-center" });

                return false;
            }

            var url = $(this).attr('href');
            if (!url || url == '#' || /^javascript|\(|\)/i.test(url)) {
                return;
            }

            if ($(this).attr('target') == '_blank') {
                return;
            }

            if ($(this).hasClass('iframes-pass-url')) {
                return;
            }

            if (window.pass_urls) {
                for (var i in window.pass_urls) {
                    if (url.indexOf(window.pass_urls[i]) > -1) {
                        return true;
                    }
                }
            }
            var icon = '<i class="fa fa-file-text"></i>';
            if ($(this).find('i.fa').size()) {
                icon = $(this).find('i.fa').prop("outerHTML");
            }
            var span = $(this).find('span');
            
            var path = url.replace(/^(https?:\/\/[^\/]+?)(\/.+)$/,'$2');

            var id = path == window.home_uri ? '_admin_dashboard' : path.replace(/\W/g, '_');
            addTabs({
                id: id,
                title: span.size() ? span.text() : $(this).text().length ? $(this).text() : '*',
                close: true,
                url: url,
                urlType: 'absolute',
                icon: icon
            });

            $(this).attr('data-pageid', id);

            var toggle = false;
            if ($(this).parents('.dropdown').size() && (toggle = $(this).parents('.dropdown').find('.dropdown-toggle'))) {
                toggle.trigger('click');
            }

            if ($(this).parents('.sidebar-form') && (toggle = $(this).parents('.sidebar-form').find('.input-group-btn button'))) {
                toggle.trigger('click');
            }

            return false;
        });

        if (window == top) {
            addTabs({
                id: '_admin_dashboard',
                title: window.home_title,
                close: false,
                url: window.home_uri,
                urlType: 'absolute',
                icon: '<i class="fa ' + window.home_icon + '"></i>'
            });
        } else {
            location.href = window.home_uri;
            $('body').html('....');
        }

        $('body').on('click', '.main-header a.logo', function () {
            return false;
        });

        $('.navbar-custom-menu').css('background-color', $('.main-header .navbar').css('background-color'));

        $('.navbar-custom-menu').show(); // delete it in future

        if (!$(".navbar-custom-menu>ul>*:first").hasClass('tab-options')) {
            $(".navbar-custom-menu>ul>*:first").before($('.navbar-custom-menu>ul>li.tab-options'));
        }

        $('.content-tabs').css({
            'left': window.tabs_left + 'px',
            'width': '100%'
        });

        setTimeout(function () {
            $('.container-refresh').off('click');
        }, 1000);

        window.handleIframeContent = function ()
        {
            $(".tab_iframe").css({
                height: "100%",
                width: "100%"
            });
        }

        $('.content-wrapper,#app,#tab-content').css('height','100%');

    });
</script>

@endsection
