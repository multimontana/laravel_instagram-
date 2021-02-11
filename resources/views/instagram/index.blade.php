<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @include('assets.css')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>
<body>
<div class="container">
    <div class="insta-post-header">
        <h2><i class="fab fa-instagram" aria-hidden="true"></i>
            <input type="text" class="puple"></h2>
    </div>
    <div class="content-posts-instagram">

    </div>
    <div class="row append-loader"></div>
    @include('assets.js')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        function showLoader() {
            $('.extorio_load_more_loader_item').remove();
            $('.append-loader').append(`<div class="extorio_load_more_loader_item"></div>`);
        }

        function hideLoader() {
            $('.extorio_load_more_loader_item').remove();
        }

        let worker = new Worker('{{asset("/js/worker/instagram.js")}}'),
            loader = true,
            post_open = true;
        worker.postMessage({"method": "append-instagram-id", "id": "{{$id}}", "token": "{{$token}}"});

        worker.onmessage = function (event) {
            if (!event.data.end) {
                switch (event.data.method) {
                    case 'load-index-page':
                        $('.content-posts-instagram').append(`
                                                             <div class="container_insta_post" data-shortcode="${event.data.res.shortcode}">
                                                                <div class="post-icon-insta"><i class="${event.data.res.post_data.post_icon}"></i></div>
                                                                 <img src="${event.data.res.display_url}"
                                                                      alt="Avatar" class="image_insta_post" style="width:100%; height: 100%">
                                                                 <div class="middle_insta_post">
                                                                     <div class="icons-comment-like">
                                                                         <div><i class="${event.data.res.post_data.like_icon}" style="color: white" aria-hidden="true"></i><span style="margin-right: 25px">${event.data.res.post_data.like_count}</span>
                                                                         </div>
                                                                         <div><i class="fa fa-comment" aria-hidden="true" style="color: white"></i><span>${event.data.res.comment_count}</span></div>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                            `);
                        break;
                    case 'get-post-by-shortcode':
                        if (event.data.res.videos.length || event.data.res.images.length) {
                            var videos = ``,
                                data_slide = ``;
                            event.data.res.videos.forEach(function (video, i) {
                                data_slide += `<li style="margin: 5px; width: 7px; height: 7px; border-radius: 50%;"  data-target="#myCarousel" data-slide-to="${i}" class="${!i ? 'active' : ''}"></li>`;
                                videos += `
                                                <div class="carousel-item ${!i ? 'active' : ''} instagram-video-content">
                                                        <i class="playing-video-instagram fa fa-play vid-${i}" data-id="${i}"></i>
                                                    <video style="width: 100%" id="vid-${i}" class="stop-video-instagram">
                                                        <source src="${video}">
                                                    </video>
                                                </div>`;
                            });
                            event.data.res.images.forEach(function (image, i) {
                                data_slide += `<li style="margin: 5px; width: 7px; height: 7px;border-radius: 50%;"  data-target="#myCarousel" data-slide-to="${i}" class="${!i ? 'active' : ''}"></li>`;
                                videos += `
                                                <div class="carousel-item ${!i ? 'active' : ''} instagram-video-content">
                                                    <img src="${image}" alt="icon" width="100%">
                                                </div>`;
                            });
                            var user_text = ``;
                            event.data.res.user.texts.forEach(function (item) {
                                var _user_text = `<div class="d-flex mb-20 text-left">`;
                                _user_text += `<div><a href="https://www.instagram.com/${event.data.res.user.username}/" target="_blank"><img src="${event.data.res.user.display_url}" alt="icon" width="32" height="32" style="border-radius: 50%; margin-right: 10px"></a></div>`;
                                _user_text += `<div><a href="https://www.instagram.com/${event.data.res.user.username}/" target="_blank"><b>${event.data.res.user.username}</b></a> ${findHashtags(item.node.text)}</div><br>`
                                _user_text += `</div>`;
                                user_text += _user_text;
                            });

                            var owner_comment = ``;
                            event.data.res.comments.forEach(function (item, i) {
                                var _owner_comment = `<div class="d-flex mb-10 mt-10 text-left">`;
                                _owner_comment += `<div><a href="https://www.instagram.com/${item.node.owner.username}/" target="_blank"><img src="${item.node.owner.profile_pic_url}" alt="icon" width="32" height="32" style="border-radius: 50%; margin-right: 10px"></a></div>`;
                                _owner_comment += `<div><a href="https://www.instagram.com/${item.node.owner.username}/" target="_blank"><b>${item.node.owner.username}</b> </a> ${findHashtags(item.node.text)}</div><br>`
                                _owner_comment += `</div>`;
                                if (item.node.edge_threaded_comments.count) {
                                    _owner_comment += `<p class="open-replace-comment" data-id="${i}">___ View replies (${item.node.edge_threaded_comments.count})</p>`;
                                    item.node.edge_threaded_comments.edges.forEach(function (replace) {
                                        _owner_comment += `<div class="d-none replace-one text-left open-p-${i}"  style="margin-left: 25px">`;
                                        _owner_comment += `<div><a href="https://www.instagram.com/${replace.node.owner.username}/" target="_blank"><img src="${replace.node.owner.profile_pic_url}" alt="icon" width="32" height="32" style="border-radius: 50%; margin-right: 10px"></a></div>`;
                                        _owner_comment += `<div><a href="https://www.instagram.com/${replace.node.owner.username}/" target="_blank"><b>${replace.node.owner.username}</b> </a> ${findHashtags(replace.node.text)}</div><br>`
                                        _owner_comment += `</div>`;
                                    })
                                }
                                owner_comment += _owner_comment;
                            });

                            Swal.fire({
                                title: '',
                                showConfirmButton: false,
                                html: `
                                        <div class="download-post-file"><i class="fas fa-download"></i></div>
                                        <div class="close-modal"><i class="far fa-times-circle"></i></div>
                                        <div class="content-dialog-post-insta">


                                                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                                 ${videos}
                                                </div>


                                                <div class="comment-content-date">
                                                    <div class="comment-content">
                                                        ${user_text}
                                                        ${owner_comment}
                                                    </div>

                                                </div>

                                        </div>
                                     `,
                                width: 800,
                            });
                            $('#myCarousel').slick({
                                dots: false,
                                prevArrow: '<button><i class="fas fa-chevron-circle-left"></i></button>',
                                nextArrow: '<button><i class="fas fa-chevron-circle-right"></i></button>'
                            });
                        }
                        break;
                }
            } else {
                post_open = true;
                loader = true;
            }
            hideLoader();
        };

        worker.onerror = function () {
            loader = true;
            post_open = true;
            hideLoader();
        };

        window.onscroll = function () {
            if (parseInt(window.innerHeight + window.scrollY + 2) >= document.body.offsetHeight && loader) {
                showLoader();
                worker.postMessage({"method": "load-index-page"});
                loader = false;
            }
        };

        function findHashtags(searchText) {
            var regexp = /\B\#\w\w+\b/g,
                result = searchText.match(regexp);
            if (result) {
                searchText = ' ' + searchText;
                result.forEach(function (item) {
                    var _item = item.replace('#', ' #');
                    _item = _item.replace(' #', ' ');
                    var original_item = item.replace('#', '');
                    searchText = searchText.replace('#', ' ');
                    searchText = searchText.replace(` ${_item}`, ` <a href="https://www.instagram.com/explore/tags/${original_item}/" target="_blank">${_item}</a> `);
                });
            }
            searchText = searchText.replace(/target="_blank"> /gi, `target="_blank"> #`);
            return searchText;
        }

        //get all usernames for autocomplete
        let globalUsers = []
        $(function () {
            var cache = {};
            $(".puple").autocomplete({
                minLength: 1,
                source: function (request, response) {
                    var term = request.term;
                    // (async () => {
                    //     const rawResponse = await fetch('/username', {
                    //         method: 'POST',
                    //         headers: {
                    //             'Accept': 'application/json',
                    //             'Content-Type': 'application/json'
                    //         },
                    //         body: JSON.stringify({username: term})
                    //     });
                    //     let users = [];
                    //     const content = await rawResponse.json();
                    //     content.users.forEach(user => {
                    //         users.push({
                    //             value: user.user.full_name,
                    //             id: user.user.pk
                    //         })
                    //     })
                    // })();
                    //test request from front by CORS
                    $.getJSON(`https://www.instagram.com/web/search/topsearch/?context=blended&rank_token=0.6738022034184186&include_reel=true&limit=5`, {query: term}, function (data) {
                        let users = [];
                        data.users.forEach(async (user) => {
                            users.push({
                                value: user.user.full_name,
                                id: user.user.pk
                            })
                        })
                        globalUsers = users
                        response(users);
                    });
                }
            });
            $('.puple').on('blur', function () {
                globalUsers.forEach(item => {
                    if (item.value == $(".puple").val()) {
                        window.location.href = '/' + item.id
                    }
                })
            })

            $(document).on('click', '.container_insta_post', function () {
                if (post_open) {
                    post_open = false;
                    let shortcode = $(this).attr('data-shortcode');
                    worker.postMessage({"method": "get-post-by-shortcode", "shortcode": shortcode});
                }
            });
            $(document).on('click', '.playing-video-instagram', function () {
                let id = 'vid-' + $(this).attr('data-id');
                $(this).addClass('d-none');
                $(`#${id}`).get(0).play();
            });
            $(document).on('click', '.stop-video-instagram', function () {
                this.pause();
                $(`.${this.id}`).removeClass('d-none');
            });
            $(document).on('click', '.open-replace-comment', function () {
                let id = $(this).attr('data-id');
                $(this).parent().find(`.open-p-${id}`).removeClass('d-none').addClass('d-flex');
                $(this).addClass('d-none');
            });
            $(document).on('click', '.click-download-post-videos', function () {
                let videos = $(this).parents('.content-dialog-post-insta').find('.active video source');
                $.each(videos, function (i, video) {
                    window.open($(video).attr('src'), 'download');
                    return false;
                });
            });
            $(document).on('click', '.close-modal', function () {
                Swal.close()
            });
            $(document).on('click', '.download-post-file', function () {
                let link_img = $(this).parent().find("#myCarousel .slick-active img").attr('src'),
                    link_vid = $(this).parent().find("#myCarousel .slick-active source").attr('src');
                if (link_img) {
                    window.open(link_img, "download")
                } else if (link_vid) {
                    window.open(link_vid, "download")
                }
            });
        })
    </script>

</div>
</body>
</html>

