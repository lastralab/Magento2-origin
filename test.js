hidePopup: function () {
            var defaultSearchBlock = this.showPopup(),
                inputWrapper = '[data-amsearch-js="search-wrapper-input"]';

            if (this.autoComplete.is(':hidden')) {
                this.searchLabel.removeClass('active');
            }

            this.autoComplete.hide();
            $('[data-amsearch-js="overlay"], [data-amsearch-js="close"], [data-amsearch-js="loupe"]').hide();
            this.searchForm.find('.input-text').attr('placeholder', $.mage.__('Search entire store here...'));
            this.searchForm.removeClass('-opened');

            if (this.windowWidth >= this.mobileView) {
                $(inputWrapper).css('width', '100%');
                this.searchForm.find('.search-autocomplete').css('width', defaultSearchBlock);
            }
        }
