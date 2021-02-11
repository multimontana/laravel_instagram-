importScripts("../libs/Instagram.js");
var instagram = new Instagram();
self.onmessage = async (event) => {
    switch (event.data.method) {
        case 'load-index-page':
            if (Instagram.end_cursor) {
                let node = instagram.getNextPage(Instagram.end_cursor);
                await node.forEach(function (item) {
                    fetch("https://www.instagram.com/web/search/topsearch/?context=blended&rank_token=0.6738022034184186&include_reel=true&limit=5&query=vvvv", {
                        "headers": {
                            "accept": "application/json, text/javascript, */*; q=0.01",
                            "accept-language": "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
                            "sec-fetch-dest": "empty",
                            "sec-fetch-mode": "cors",
                            "sec-fetch-site": "cross-site"
                        },
                        "referrer": "http://127.0.0.1:8000/",
                        "referrerPolicy": "strict-origin-when-cross-origin",
                        "body": null,
                        "method": "GET",
                        "mode": "cors",
                        "credentials": "omit"
                    });
                    self.postMessage({"res": item, 'method': 'load-index-page'}, null);
                });
            }
            break;
        case 'get-post-by-shortcode':
            let postInfo = instagram.getPostByShortcode(event.data.shortcode);
            await self.postMessage({"res": postInfo, 'method': 'get-post-by-shortcode'}, null);
            break;
        case 'append-instagram-id':
            Instagram.user_id = event.data.id;
            let node = instagram.getNextPage();
            await node.forEach(function (item) {
                self.postMessage({"res": item, 'method': 'load-index-page'}, null);
            });
            break;
    }
    self.postMessage({"end": true}, null);
};
