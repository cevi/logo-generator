/* global document, window, fetch, FormData */

export default class ApiLinks {
    async postData(url = '/api.php', data = {}) { // eslint-disable-line class-methods-use-this
        const formData = new FormData();
        Object.entries(data).forEach(([key, value]) => {
            formData.append(key, value);
        });

        await fetch(url, {
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            body: formData // body data type must match "Content-Type" header
        }).then((response) => {
            if (response.status !== 200) {
                console.log(response);
            }
        });
    }

    prepareLinkElements() {
        this.links = [
            {
                link: document.getElementById('svg-logo-link'),
                image: 'svg',
                type: this.typeLogo
            },
            {
                link: document.getElementById('png-logo-link'),
                image: 'png',
                type: this.typeLogo
            },
            {
                link: document.getElementById('jpg-logo-link'),
                image: 'jpg',
                type: this.typeLogo
            },
            {
                link: document.getElementById('svg-claim-link'),
                image: 'svg',
                type: this.typeClaim
            },
            {
                link: document.getElementById('png-claim-link'),
                image: 'png',
                type: this.typeClaim
            }
        ];
    }

    handleLinkClick(self, item) { // eslint-disable-line class-methods-use-this
        item.link.addEventListener('click', () => {
            if (item.type === self.typeClaim) {
                self.postData('/api.php', {
                    session_id: document.session_id,
                    type: item.type,
                    image_type: item.image,
                    logo_left: window.generator_data.logo_text_left,
                    logo_right: window.generator_data.logo_text_right,
                    logo_right_second: window.generator_data.logo_text_right_second,
                    claim_left: window.generator_data.claim_text_left,
                    claim_right: window.generator_data.claim_text_right,
                    color: window.generator_data.color
                });
            } else if (item.type === self.typeLogo) {
                self.postData('/api.php', {
                    session_id: document.session_id,
                    type: item.type,
                    image_type: item.image,
                    logo_left: window.generator_data.logo_text_left,
                    logo_right: window.generator_data.logo_text_right,
                    logo_right_second: window.generator_data.logo_text_right_second,
                    color: window.generator_data.color
                });
            }
        });
    }

    constructor() {
        const self = this;
        this.typeLogo = 'logo';
        this.typeClaim = 'claim';
        this.prepareLinkElements();

        Object.values(this.links).forEach((item) => {
            this.handleLinkClick(self, item);
        });
    }
}
