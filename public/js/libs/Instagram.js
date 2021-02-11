importScripts('../libs/XMLRequest.js');
class Instagram extends XMLRequest {

    static node = [];

    static user_id;
    static end_cursor;

    getNextPage(end_cursor = '') {
        try {
            var data = this.to(`https://www.instagram.com/graphql/query/`).get({
                "query_id": "17888483320059182",
                "id": Instagram.user_id,
                "first": 12,
                "after": end_cursor
            }).json();
            Instagram.end_cursor = data.data.user.edge_owner_to_timeline_media.page_info.end_cursor;
            Instagram.node = [];
            data.data.user.edge_owner_to_timeline_media.edges.forEach(function (item) {
                Instagram.node.push({
                    "shortcode": item.node.shortcode,
                    "display_url": item.node.thumbnail_resources[item.node.thumbnail_resources.length - 3]['src'],
                    "comment_count": item.node.edge_media_to_comment.count,
                    "like_count": item.node.edge_media_preview_like.count,
                    "post_data": {
                        "like_count": item.node.video_view_count ? item.node.video_view_count : item.node.edge_media_preview_like.count,
                        "like_icon": item.node.video_view_count ? 'fas fa-play' : 'fa fa-heart',
                        "post_icon": item.node.video_view_count ? 'fas fa-video"' : 'fa fa-clone'
                    }
                });
            });
        } catch (e) {
            return [];
        }

        return Instagram.node;
    }

    getPostByShortcode(shortcode) {
        let post = this.to(`https://www.instagram.com/graphql/query/`).get({
            "query_hash": "7da1940721d75328361d772d102202a9",
            "variables": JSON.stringify({
                "shortcode": shortcode,
                "child_comment_count": "40",
                "fetch_comment_count": "40",
                "parent_comment_count": "40",
                "has_threaded_comments": true,
            })
        }).json();
        let array = {};
        array.videos = [];
        array.images = [];
        array.user = {};
        array.comments = {};
        array.post = {};
        array.user.display_url = post.data.shortcode_media.owner.profile_pic_url;
        array.user.username = post.data.shortcode_media.owner.username;
        array.user.id = post.data.shortcode_media.owner.id;
        array.user.texts = post.data.shortcode_media.edge_media_to_caption.edges;
        array.comments = post.data.shortcode_media.edge_media_to_parent_comment.edges;
        array.post.like = post.data.shortcode_media.video_view_count ? post.data.shortcode_media.video_view_count + ' <b>views</b>' : post.data.shortcode_media.edge_media_preview_like.count + ' <b>likes</b>';
        if (post.data.shortcode_media.edge_sidecar_to_children) {
            post.data.shortcode_media.edge_sidecar_to_children.edges.forEach(function (item) {
                if (item.node.video_url) {
                    array.videos.push(item.node.video_url);
                } else if (item.node.display_url) {
                    array.images.push(item.node.display_url);
                }

            })
        } else if (post.data.shortcode_media.video_url) {
            array.videos.push(post.data.shortcode_media.video_url);
        } else {
            array.images.push(post.data.shortcode_media.display_url);
        }
        return array;
    }
}
