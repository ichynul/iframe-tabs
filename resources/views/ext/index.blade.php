@extends('admin::index', ['header' => $header])

@section('content')
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
        window.iframes_index = '{{ $iframes_index }}';

        window.openPop = function(url, title) {
            layer.open({
                type: 2,
                title: title,
                anim: 2,
                closeBtn: 1,
                shade: false,
                maxmin: true, //开启最大化最小化按钮
                area: [$('#tab-content').width() + 'px', ($('#tab-content').height() - 5) + 'px'],
                content: url,
                offset: 'rb'
            });
        }

        window.openTab = function(url, title, icon, page_id, close, urlType) {
            if (!url) {
                alert('url is empty.');
                return;
            }
            addTabs({
                id: page_id || url.replace(/\W/g, '_'),
                title: title || 'New page',
                close: close != false && close != 0,
                url: url,
                urlType: urlType || 'absolute',
                icon: '<i class="fa ' + (icon || 'fa-file-text') + '"></i>'
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

        $('body').on('click', '.sidebar-menu li a,.navbar-nav>li a,.sidebar .user-panel a', function() {
            var url = $(this).attr('href');
            if (!url || url == '#' || /^javascript|\(|\)/i.test(url)) {
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
            addTabs({
                id: '_admin_dashboard',
                title: window.home_title,
                close: false,
                url: window.home_uri,
                urlType: 'absolute',
                icon: '<i class="fa ' + window.home_icon + '"></i>'
            });
        } else {
            if (location.href == window.iframes_index) {
                $('body').html('....');
                location.href = window.home_uri;
            }
        }

        $('body').on('click', '.main-header a.logo', function() {
            return false;
        });

        $('.content-tabs').css('width', $('.main-header').width() - $('.main-header .logo').width() - $('.navbar-custom-menu').width() - 80);

        $('.content-tabs-divider,.navbar-custom-menu').css('background-color', $('.main-header .navbar').css('background-color'));

        $('.navbar-custom-menu,.content-tabs-divider').show();

        if (!$(".navbar-custom-menu>ul>*:first").hasClass('tab-options')) {
            $(".navbar-custom-menu>ul>*:first").before($('.navbar-custom-menu>ul>li.tab-options'));
        }

        $('.main-header .logo').resize(function() {
            adjust();
        });

        adjust();
    });

    function adjust() {
        if ($('.main-header').outerWidth() - $('.main-header .logo').outerWidth() <= 100) {
            $('.content-tabs').css('width', $('.main-header').outerWidth() - $('.navbar-custom-menu').outerWidth() - 46);
        } else {
            $('.content-tabs').css('width', $('.main-header').width() - $('.main-header .logo').outerWidth() - $('.navbar-custom-menu').outerWidth() - 46);
        }
    }

    (function($, h, c) {
        var a = $([]),
            e = $.resize = $.extend($.resize, {}),
            i,
            k = "setTimeout",
            j = "resize",
            d = j + "-special-event",
            b = "delay",
            f = "throttleWindow";
        e[b] = 250;
        e[f] = true;
        $.event.special[j] = {
            setup: function() {
                if (!e[f] && this[k]) {
                    return false;
                }
                var l = $(this);
                a = a.add(l);
                $.data(this, d, {
                    w: l.width(),
                    h: l.height()
                });
                if (a.length === 1) {
                    g();
                }
            },
            teardown: function() {
                if (!e[f] && this[k]) {
                    return false;
                }
                var l = $(this);
                a = a.not(l);
                l.removeData(d);
                if (!a.length) {
                    clearTimeout(i);
                }
            },
            add: function(l) {
                if (!e[f] && this[k]) {
                    return false;
                }
                var n;

                function m(s, o, p) {
                    var q = $(this),
                        r = $.data(this, d);
                    r.w = o !== c ? o : q.width();
                    r.h = p !== c ? p : q.height();
                    n.apply(this, arguments);
                }
                if ($.isFunction(l)) {
                    n = l;
                    return m;
                } else {
                    n = l.handler;
                    l.handler = m;
                }
            }
        };

        function g() {
            i = h[k](function() {
                    a.each(function() {
                        var n = $(this),
                            m = n.width(),
                            l = n.height(),
                            o = $.data(this, d);
                        if (m !== o.w || l !== o.h) {
                            n.trigger(j, [o.w = m, o.h = l]);
                        }
                    });
                    g();
                },
                e[b]);
        }
    })(jQuery, this);
</script>

@endsection