/**
 * Download Form plugin for Craft CMS
 *
 * DownloadFormField Field JS
 *
 * @author    Pixel&Code
 * @copyright Copyright (c) 2019 Pixel&Code
 * @link      https://www.pixelcode.nl
 * @package   DownloadForm
 * @since     2.0.0
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "DownloadFormField",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function(id) {
            var _this = this;

            $(function () {

/* -- _this.options gives us access to the $jsonVars that our FieldType passed down to us */

                $(document).ready(function () {
                    $('.open-download-file-selector-button').on('click', function () {
                        var assetSource = $(this).data('assetSource');
                        Craft.createElementSelectorModal('Asset', {
                            resizable:          true,
                            sources:            ['folder:' + assetSource],
                            criteria:           { status: null },
                            multiSelect:        false,
                            disabledElementIds: [],
                            disableOnSelect:    true,
                            onCancel:           function(){},
                            onSelect:           function(entries){
                                if (entries.length > 0) {
                                    $('.download-file-field').val(entries[0].url);
                                    $('.selected-file').text(entries[0].url)
                                }
                            }
                        });
                    });

                    var initEnableState = function () {
                        var value = $('.download-form-fields-group .enable-switch .lightswitch input[type="hidden"]').val();
                        var enabled = value == '1';

                        var fieldsGroup = $('.download-form-fields-group .download-form-fields');
                        if (enabled) {
                            fieldsGroup.slideDown(200);
                        } else {
                            fieldsGroup.slideUp(200);
                        }
                    };

                    $('.download-form-fields-group .enable-switch .lightswitch-container').on('click', initEnableState);
                    initEnableState();

                    var initMailChimpState = function () {
                        var value = $('.download-form-fields-group .mailchimp-subscribe-switch .lightswitch input[type="hidden"]').val();
                        var enabled = value == '1';

                        var fieldGroups = $('.download-form-fields-group .mailchimp-field');
                        if (enabled) {
                            fieldGroups.slideDown(200);
                        } else {
                            fieldGroups.slideUp(200);
                        }
                    };

                    $('.download-form-fields-group .mailchimp-subscribe-switch .lightswitch-container').on('click', initMailChimpState);
                    initMailChimpState();
                });

            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
