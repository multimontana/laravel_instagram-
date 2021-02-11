class XMLRequest {
    static url;
    static http;
    static headers;
    static setHeaders = function () {
        Object.entries(XMLRequest.headers).forEach(entry => {
            const [key, value] = entry;
            XMLRequest.http.setRequestHeader(key, value);
        });
    };

    constructor() {
        XMLRequest.url = '';
        XMLRequest.http = new XMLHttpRequest();
        XMLRequest.headers = {}
    }

    to(url) {
        let _this = this;
        XMLRequest.url = url;
        return {
            headers: function (headers = {}) {
                XMLRequest.headers = headers;
                return this;
            },
            get: function (data = {}) {
                let parameter = new URLSearchParams(data);
                if (parameter.toString().length) {
                    XMLRequest.url += '?' + parameter.toString();
                }
                return _this.response('GET');
            },
            post: function (data = {}) {
                return _this.response('POST', data);
            },
            put: function (data = {}) {
                return _this.response('PUT', data);
            },
            patch: function (data = {}) {
                return _this.response('PATCH', data);
            },
            option: function (data = {}) {
                return _this.response('OPTION', data);
            }
        }
    }

    response(method, data = {}) {
        XMLRequest.http.open(method, XMLRequest.url, false);
        XMLRequest.setHeaders();
        XMLRequest.headers = {};
        XMLRequest.http.send(JSON.stringify(data));
        return {
            json: function () {
                try {
                    return JSON.parse(XMLRequest.http.response);
                } catch (e) {
                    return {
                        error: true,
                        message: 'SyntaxError: Unexpected token < in JSON at position 0'
                    }
                }

            },
            response: XMLRequest.http.response,
            code: XMLRequest.http.status,
            http: XMLRequest.http
        }
    }
}

