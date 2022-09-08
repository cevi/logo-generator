/* global document, XMLSerializer, window */

import Canvg from 'canvg';
import $ from 'jquery';
import TextToSVG from 'text-to-svg';

/**
 * @author Beat Temperli v/o Zottel
 */
export default class Logo {
    constructor() {
        const self = this;

        this.testModeOn = false;
        this.claimData = null;
        this.$generator = null;
        this.textToSvg = null;
        this.Serializer = new XMLSerializer();
        this.color = {
            print: {
                black: '#000000',
                blue: '#0026ff',
                red: '#ff0033'
            },
            web: {
                black: '#141412',
                blue: '#323394',
                red: '#c41333'
            },
            black: {
                black: '#141412',
                blue: '#141412',
                red: '#141412'
            },
            current: {}
        };

        TextToSVG.load('/assets/fonts/montserrat/Montserrat-ExtraBold.ttf', (err, textToSvg) => {
            self.textToSvg = textToSvg;
            self.init();
        });
    }

    loadGenerator() {
        this.$generator = $('.logo-generator');
        this.$testSvg = $('#svg-test');
        this.$shareLink = $('.js-share-link');
    }

    /**
     * Build the svg-claim.
     *
     * @returns {string}
     */
    buildClaim() {
        const claimBoxMargin = 30;
        const claimBoxPaddingLeft = 50;
        const claimBoxPaddingTop = 131.6191;
        const widthHalfSpace = 32;
        const widthHalfChar = 16;

        // The Claim Text
        const claimTextLeftGenerated = this.generateText(this.textClaimLeft, 110, claimBoxMargin + claimBoxPaddingLeft, claimBoxMargin + claimBoxPaddingTop);
        const textClaimFull = `${this.textClaimLeft}${this.textClaimRight}`;
        const claimTextGenerated = this.generateText(textClaimFull, 110, claimBoxMargin + claimBoxPaddingLeft, claimBoxMargin + claimBoxPaddingTop);

        // The Claim Sizes
        const claimTextLeftWidth = this.getPathWidth(claimTextLeftGenerated, this.textClaimLeft);
        const claimTextWidth = this.getPathWidth(claimTextGenerated, textClaimFull);
        const claimBoxWidth = claimTextWidth + (claimBoxPaddingLeft * 2);

        // The Logo Text & Icon
        const logoTextCorrection = 8;
        const logoTextPadding = 50;
        const logoShowIcon = this.textLogoRight !== '';
        const logoTextLeft = this.generateText(this.textLogoLeft, 110, logoTextPadding - logoTextCorrection, 349.6191);
        const logoTextLeftWidth = this.getPathWidth(logoTextLeft, this.textLogoLeft);
        let icon = '';
        let logoTextRight = '';
        let logoTextRightSecond = '';
        let iconWidth = 0;
        let logoBoxWidth = (logoTextPadding * 2) + logoTextLeftWidth + logoTextCorrection;

        if (logoShowIcon) {
            // The icon.
            icon = this.generateLogo(logoTextPadding + logoTextLeftWidth + (logoTextPadding / 2), 37);
            iconWidth = this.getPathWidth(icon, '');

            // The right text.
            let logoTextRightSecondWidth = 0;

            if (this.textLogoRightSecond !== '') {
                logoTextRight = this.generateText(this.textLogoRight, 48, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding, 83.5);
                logoTextRightSecond = this.generateText(this.textLogoRightSecond, 48, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding, 127.5);
                logoTextRightSecondWidth = this.getPathWidth(logoTextRightSecond, this.textLogoRightSecond);
            } else {
                logoTextRight = this.generateText(this.textLogoRight, 110, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding, 127.5);
            }

            let logoTextRightWidth = this.getPathWidth(logoTextRight, this.textLogoRight);

            if (logoTextRightSecondWidth > logoTextRightWidth) {
                logoTextRightWidth = logoTextRightSecondWidth;
            }

            // The full logo width.
            logoBoxWidth = (logoTextPadding * 3) + iconWidth + logoTextLeftWidth + logoTextRightWidth + logoTextCorrection;
        }

        // Image box
        let imageWidth = claimBoxWidth + (claimBoxMargin * 2);
        let triangleCenter = claimBoxMargin + claimBoxPaddingLeft + claimTextLeftWidth + widthHalfChar;
        let startLogo = claimBoxWidth - Math.round((logoBoxWidth / 5) * 4);

        if (logoBoxWidth > 400) {
            startLogo = claimBoxWidth - Math.round((logoBoxWidth / 3) * 2);
        }

        if (this.splitSpaceForTriangle) {
            triangleCenter = claimBoxMargin + claimBoxPaddingLeft + claimTextLeftWidth + widthHalfSpace;
        }

        const claimLeftLastChar = this.textClaimLeft.charAt(this.textClaimLeft.length - 1);
        if (claimLeftLastChar === '+' || claimLeftLastChar === '=' || claimLeftLastChar === '-') {
            triangleCenter = claimBoxMargin + claimBoxPaddingLeft + claimTextLeftWidth + widthHalfChar;
        }

        // startLogo is always at least 120 minus the triangleCenter
        if (startLogo + 120 > triangleCenter) {
            startLogo = triangleCenter - 120;
        }

        if (this.testModeOn) {
            console.log(`startLogo: ${startLogo}`);
            console.log(`logoBoxWidth: ${logoBoxWidth}`);
            console.log(`claimBoxWidth: ${claimBoxWidth}`);
        }

        if (startLogo < claimBoxPaddingLeft) {
            // @see https://github.com/cevi/logo-generator/issues/16
            // if startLogo < 0, p.e. -200, startLogo needs to be claimBoxPaddingLeft, means: 50. difference = 250
            // if 0 < startLogo < claimBoxPaddingLeft, p.e. 20, startLogo needs to be 50. difference = 30
            const difference = startLogo >= 0 ? claimBoxPaddingLeft - startLogo : startLogo * -1 + claimBoxPaddingLeft;
            startLogo += difference;
        }

        const endOfLogo = startLogo + logoBoxWidth;

        // If imageWidth is smaller than the endLogo, adjust it.
        if (imageWidth < endOfLogo + claimBoxMargin) {
            imageWidth = endOfLogo + claimBoxMargin;
        }

        // Generate SVG
        let svg = `<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="${imageWidth}px" height="430px" viewBox="0 0 ${imageWidth} 430" enable-background="new 0 0 ${imageWidth} 430" xml:space="preserve">`; // eslint-disable-line max-len

        // Claim
        svg += `<polygon fill="#FFFFFF" points="${claimBoxWidth + claimBoxMargin},208 ${triangleCenter + 32},208 ${triangleCenter},158 ${triangleCenter - 32},208 ${claimBoxMargin},208 ${claimBoxMargin},${claimBoxMargin} ${claimBoxWidth + claimBoxMargin},${claimBoxMargin}"/>`; // eslint-disable-line max-len
        svg += claimTextGenerated;

        // Logo
        svg += `<polygon fill="#FFFFFF" points="${endOfLogo},400 ${startLogo},400 ${startLogo},222 ${triangleCenter - 24},222 ${triangleCenter},185 ${triangleCenter + 24},222 ${endOfLogo},222"/>`; // eslint-disable-line max-len

        if (logoShowIcon) {
            const startIcon = startLogo + logoTextPadding + logoTextLeftWidth + (logoTextPadding / 2);
            icon = this.generateLogo(startIcon, 259);
            svg += icon;
        }

        svg += this.generateText(this.textLogoLeft, 110, startLogo + (logoTextPadding - logoTextCorrection), 349.6191); // eslint-disable-line max-len

        if (logoShowIcon) {
            if (this.textLogoRightSecond !== '') {
                svg += this.generateText(this.textLogoRight, 48, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding + startLogo, 305.6191);
                svg += this.generateText(this.textLogoRightSecond, 48, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding + startLogo, 349.6191);
            } else {
                svg += this.generateText(this.textLogoRight, 110, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding + startLogo, 349.6191); // eslint-disable-line max-len
            }
        }

        svg += '</svg>';
        return svg;
    }

    /**
     * Build the svg logo.
     *
     * @returns {string}
     */
    buildLogo() {
        const textCorrection = 8;
        const logoTextPadding = 50;
        const showIcon = this.textLogoRight !== '';

        // The left text.
        const logoTextLeft = this.generateText(this.textLogoLeft, 110, logoTextPadding - textCorrection, 127.5);
        const logoTextLeftWidth = this.getPathWidth(logoTextLeft, this.textLogoLeft);

        let icon = '';
        let logoTextRight = '';
        let logoTextRightSecond = '';
        let logoWidth = (logoTextPadding * 2) + logoTextLeftWidth + textCorrection;

        if (showIcon) {
            // The icon.
            icon = this.generateLogo(logoTextPadding + logoTextLeftWidth + (logoTextPadding / 2), 37);
            const iconWidth = this.getPathWidth(icon, '');

            // The right text.
            let logoTextRightSecondWidth = 0;

            if (this.textLogoRightSecond !== '') {
                logoTextRight = this.generateText(this.textLogoRight, 48, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding, 83.5);
                logoTextRightSecond = this.generateText(this.textLogoRightSecond, 48, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding, 127.5);
                logoTextRightSecondWidth = this.getPathWidth(logoTextRightSecond, this.textLogoRightSecond);
            } else {
                logoTextRight = this.generateText(this.textLogoRight, 110, logoTextPadding + logoTextLeftWidth + iconWidth + logoTextPadding, 127.5);
            }

            let logoTextRightWidth = this.getPathWidth(logoTextRight, this.textLogoRight);

            if (logoTextRightSecondWidth > logoTextRightWidth) {
                logoTextRightWidth = logoTextRightSecondWidth;
            }

            // The full logo width.
            logoWidth = (logoTextPadding * 3) + iconWidth + logoTextLeftWidth + logoTextRightWidth + textCorrection;
        }

        let svg = `<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="${logoWidth}px" height="178px" viewBox="0 0 ${logoWidth} 178" enable-background="new 0 0 ${logoWidth} 178" xml:space="preserve">`; // eslint-disable-line max-len

        svg += `<polygon fill="#FFFFFF" points="${logoWidth},178 0,178 0,0 ${logoWidth},0"/>`;
        svg += logoTextLeft;

        // Add right text & icon if needed.
        if (showIcon) {
            svg += icon;
            svg += logoTextRight;
            svg += logoTextRightSecond;
        }

        // Close SVG.
        svg += '</svg>';
        return svg;
    }

    buildShareLinks() {
        let href = `${window.location.protocol}//${window.location.hostname}`;
        href = `${href}?logo-left=${this.textLogoLeft}`;
        href = `${href}&logo-right=${this.textLogoRight}`;
        href = `${href}&logo-right-second=${this.textLogoRightSecond}`;
        href = `${href}&claim-left=${this.textClaimLeft}`;
        href = `${href}&claim-right=${this.textClaimRight}`;
        href = `${href}&color=${this.color.current.name}`;

        this.$shareLink.attr('href', href);
    }

    /**
     * Generate the text as a SVG-Path.
     *
     * @param text
     * @param size
     * @param x
     * @param y
     * @returns {string}
     */
    generateText(text, size, x, y) {
        if (!this.textToSvg) {
            return '';
        }

        const correctedText = this.removeBadText(text);

        const attributes = { fill: this.color.current.black, 'font-weight': 'bold' };
        const options = {
            x,
            y,
            fontSize: size,
            attributes
        };

        const pathOutput = this.textToSvg.getSVG(correctedText, options);
        const path = $(pathOutput).find('path')[0];
        return this.Serializer.serializeToString(path);
    }

    generateLogo(xDecimal, yDecimal) { // eslint-disable-line class-methods-use-this
        const x = Math.round(xDecimal);
        const y = Math.round(yDecimal);

        let svg = '<g>';
        svg += `<path fill="${this.color.current.blue}" d="M${x}.931,${y + 36}.371c-0.181-0.641-0.267-1.301-0.327-1.85l12.602,12.581 C${x + 6}.397,${y + 44}.64,${x + 2}.158,${y + 44}.975,${x}.931,${y + 36}.371"/>`; // eslint-disable-line max-len
        svg += `<path fill="${this.color.current.red}" d="M${x + 81}.722,${y + 78}.954c-8.831,2.554-17.685,4.158-26.022,4.703l31.227,31.223l11.095-41.367 C${x + 93}.111,${y + 75}.417,${x + 87}.689,${y + 77}.222,${x + 81}.722,${y + 78}.954"/>`; // eslint-disable-line max-len
        svg += `<path fill="${this.color.current.red}" d="M${x + 109}.51,${y + 27}.399c-11.379,7.401-25.311,13.781-38.275,17.489c-17.806,5.161-35.959,6.776-49.823,4.384 l-21.157-21.185l117.507-31.565L${x + 109}.51,${y + 27}.399"/>`; // eslint-disable-line max-len
        svg += `<path fill="${this.color.current.blue}" d="M${x + 126}.756,${y + 49}.589c-8.896,10.981-23.162,18.734-46.193,25.432c-23.495,6.697-46.936,6.516-59.736-0.58 c-4.496-2.516-7.312-5.601-8.392-9.293l-6.739-16.765c0.268,0.129,0.535,0.267,0.766,0.5c13.803,7.585,39.672,7.634,65.947,0.128 c21.021-6.073,43.057-18.351,54.348-30.131V${y + 49}.589"/>`; // eslint-disable-line max-len
        svg += `<path fill="${this.color.current.blue}" d="M${x + 86}.924,${y}.621l-82.082,21.879c8.453-12.008,28.283-23.847,51.221-30.49 c6.447-1.871,20.849-4.191,30.861-3.807V${y}.621"/>`; // eslint-disable-line max-len
        svg += '</g>';

        return svg;
    }

    /**
     * Get the path with of a SVG-PATH.
     *
     * @param path
     * @param text
     * @returns {number}
     */
    getPathWidth(path, text) {
        this.$testSvg.html(path);
        const width = Math.round(this.$testSvg.children()[0].getBoundingClientRect().width);
        const correction = this.getPathWidthCorrection(text.charAt(0), text.charAt(text.length - 1));

        return width + correction;
    }

    /**
     * Get the path width correction for special characters.
     *
     * The font will not be rendered absolutely correct by the SVG-Path-Drawer. For some letters we have to add
     * some additions / minus.
     *
     * @param first
     * @param last
     * @returns {number}
     */
    getPathWidthCorrection(first, last) { // eslint-disable-line class-methods-use-this
        let correction = 0;

        const fontCharCorrections = {
            A: -8,
            C: -3,
            G: -3,
            J: -8,
            O: -3,
            Q: -3,
            S: -5,
            T: -7,
            V: -9,
            W: -5,
            X: -6,
            Y: -8,
            Z: -2
        };

        if (last === '=' || last === '+') {
            correction -= 36;
        }

        if (last === '-') {
            correction -= 24;
        }

        if (fontCharCorrections[first]) {
            correction += fontCharCorrections[first];
        }

        return correction;
    }

    /**
     * Prepare the generator, load all html-elements.
     */
    prepareGenerator() {
        this.$logoleft = $('#input-logo-left');
        this.$logoright = $('#input-logo-right');
        this.$logorightsecond = $('#input-logo-right-second');
        this.$claimleft = $('#input-claim-left');
        this.$claimright = $('#input-claim-right');
        this.$colorRadioGroup = $('input[name="color"]');
        this.colorRadio = document.logoGeneratorForm.color;

        // Set current color based on the form.
        this.color.current = this.color[this.colorRadio.value];
        this.color.current.name = this.colorRadio.value;
    }

    /**
     * Get the data for the generator.
     */
    getGeneratorData() {
        this.textLogoLeft = this.removeBadText(this.$logoleft.val().toUpperCase());
        this.textLogoRight = this.removeBadText(this.$logoright.val().toUpperCase());
        this.textLogoRightSecond = this.removeBadText(this.$logorightsecond.val().toUpperCase());
        this.textClaimLeft = this.removeBadText(this.$claimleft.val().toUpperCase());
        this.textClaimRight = this.removeBadText(this.$claimright.val().toUpperCase());

        window.generator_data = {
            logo_text_left: this.textLogoLeft,
            logo_text_right: this.textLogoRight,
            logo_text_right_second: this.textLogoRightSecond,
            claim_text_left: this.textClaimLeft,
            claim_text_right: this.textClaimRight,
            color: this.colorRadio.value
        };

        this.$logoleft.val(this.textLogoLeft);
        this.$logoright.val(this.textLogoRight);
        this.$logorightsecond.val(this.textLogoRightSecond);
        this.$claimleft.val(this.textClaimLeft);
        this.$claimright.val(this.textClaimRight);

        this.splitSpaceForTriangle = (
            this.textClaimLeft.substring(this.textClaimLeft.length - 1) === ' '
            || this.textClaimRight.substring(0, 1) === ' '
        );
    }

    removeBadText(text) { // eslint-disable-line class-methods-use-this
        let tmpText = text.toUpperCase();

        const badWords = [
            'pfadi',
            'besj',
            'pfadfinder',
            'jungwacht',
            'jubla',
            'blauring',
            'blaues kreuz',
            'pro natura',
            'naturfreunde',
            'wölfli',
            'fünkli',
            'jublini',
            'bienli',
            'kadetten',
            'sajv',
            'rover',
            'piss',
            'scheisse',
            'shit',
            'fuck',
            'ficken',
            'fick',
            'penis',
            'pimmel',
            'arschloch',
            'fotze',
            'schlampe',
            'bitch',
            'nutte',
            'titten'
        ];

        for (let i = 0; i < badWords.length; i += 1) {
            const regexp = new RegExp(badWords[i].toUpperCase(), 'g');
            tmpText = tmpText.replace(regexp, '---');
        }

        /**
         * #11 defuse bad words.
         * @see https://github.com/cevi/logo-generator/issues/11 */
        const blackList = [
            'arsch'
        ];
        const whiteList = [
            'marsch'
        ];
        if (blackList.length !== whiteList.length) {
            throw new Error('blacklist words and whitelist words have not equal length, they must match together.');
        }
        for (let i = 0; i < blackList.length; i += 1) {
            const regexp = new RegExp(`${blackList[i].toUpperCase()}(?<!${whiteList[i].toUpperCase()})`, 'g');
            tmpText = tmpText.replace(regexp, '---');
        }

        return tmpText;
    }

    /**
     * Build the images in the generator.
     */
    buildGeneratorImages() {
        const logoSvg = this.buildLogo();
        const claimSvg = this.buildClaim();
        const logoCanvas = document.getElementById('canvas-logo-output');
        const claimCanvas = document.getElementById('canvas-claim-output');

        // Add SVG to svg
        $('#svg-logo-output').html(logoSvg);
        $('#svg-claim-output').html(claimSvg);

        // Add SVG to canvas
        $('#canvas-logo-output').height('178px');
        $('#canvas-claim-output').height('430px');

        const logoCanvg = Canvg.fromString(
            logoCanvas.getContext('2d'),
            logoSvg,
            { ignoreMouse: true, ignoreAnimation: true }
        );

        const claimCanvg = Canvg.fromString(
            claimCanvas.getContext('2d'),
            claimSvg,
            { ignoreMouse: true, ignoreAnimation: true }
        );

        // render canvas
        logoCanvg.render();
        claimCanvg.render();

        // Add SVG to svg-link
        // Get svg source.
        let svgLogoSource = logoSvg;
        let svgClaimSource = claimSvg;

        // add xml declaration
        svgLogoSource = `<?xml version="1.0" standalone="no"?>\r\n${svgLogoSource}`;
        svgClaimSource = `<?xml version="1.0" standalone="no"?>\r\n${svgClaimSource}`;

        // Convert svg source to URI data scheme.
        const svgLogoLink = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svgLogoSource)}`;
        const svgClaimLink = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svgClaimSource)}`;

        // Set url value to a element's href attribute.
        $('#svg-claim-link').attr('href', svgClaimLink);
        $('#svg-logo-link').attr('href', svgLogoLink);

        // Add canvas to png
        const pngLogo = logoCanvas.toDataURL('image/png');
        $('#png-logo-output').attr('src', pngLogo);
        $('#png-logo-link').attr('href', pngLogo);

        const pngClaim = claimCanvas.toDataURL('image/png');
        $('#png-claim-output').attr('src', pngClaim);
        $('#png-claim-link').attr('href', pngClaim);

        // // Add canvas to jpg
        // No claim to jpg (claim needs to be transparent)...
        const jpgLogo = logoCanvas.toDataURL('image/jpeg');
        $('#jpg-logo-output').attr('src', jpgLogo);
        $('#jpg-logo-link').attr('href', jpgLogo);
    }

    /**
     * Initialize function
     */
    init() {
        this.loadGenerator();

        if (this.$generator.length > 0) {
            this.generatorInit = true;
            this.prepareGenerator();
            this.getGeneratorData();
            this.buildGeneratorImages();
            this.buildShareLinks();

            $('.input').on('keyup', () => {
                this.getGeneratorData();
                this.buildGeneratorImages();
                this.buildShareLinks();
            });

            this.$colorRadioGroup.on('change', () => {
                this.color.current = this.color[this.colorRadio.value];
                this.color.current.name = this.colorRadio.value;

                this.getGeneratorData();
                this.buildGeneratorImages();
                this.buildShareLinks();
            });
        }
    }
}
