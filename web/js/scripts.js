$(function () {
    wishlist = {
        anchor:[],
        addElement: function (model, itemId, typeWish, url, button) {
            $.post({
                url: url,
                data: { model: model, itemId: itemId, typeWish: typeWish},
                beforeSend: function () {
                    $(button).data('action', 'wait').attr('data-action', 'wait');
                },
                success: function (response) {
                    if (response) {
                        modelName = model.replace( /.+\\/g, "" );
                        var anchor = wishlist[modelName + "_" + typeWish];
                        $(button).data('action', 'remove').attr('data-action', 'remove');
                        $(button).data('url', response.url).attr('data-url', response.url);
                        inListCssClass = $(button).data('in-list-css-class');
                        $(button).addClass(inListCssClass);
                        $(button).attr('title', anchor.activeTitle);
                        $(button).html(anchor.active);
                        $('#count-wishlist-badge').text(response.count);
                        return true;
                    } else {
                        $(button).data('action', 'add').attr('data-action', 'add');
                        return false;
                    }
                }
            })
            .fail(function(response) {
                $(button).data('action', 'add').attr('data-action', 'add');
                return false;
            });
        },
        removeElement: function (model, itemId, typeWish, url, button) {
            $.post({
                url: url,
                data: { model: model, itemId: itemId, typeWish: typeWish},
                beforeSend: function () {
                    $(button).data('action', 'wait').attr('data-action', 'wait');
                },
                success: function (response) {
                    if (response) {
                        modelName = model.replace( /.+\\/g, "" );
                        var anchor = wishlist[modelName + "_" + typeWish];
                        $(button).data('action', 'add').attr('data-action', 'add');
                        $(button).data('url', response.url).attr('data-url', response.url);
                        inListCssClass = $(button).data('in-list-css-class');
                        $(button).removeClass(inListCssClass);
                        $(button).attr('title', anchor.unactiveTitle);
                        $(button).html(anchor.unactive);
                        $('#count-wishlist-badge').text(response.count);
                        return true;
                    } else {
                        $(button).data('action', 'remove').attr('data-action', 'remove');
                        return false;
                    }
                }
            })
            .fail(function(response) {
                $(button).data('action', 'remove').attr('data-action', 'remove');
                return false;
            });
        },
    };

    $(document).on('click', '[data-role=hal_wishlist_button]',function () {
        var self = this,
            model = $(self).data('model'),
            itemId = $(self).data('item-id'),
            action = $(self).data('action'),
            url = $(self).data('url');
            typeWish = $(self).data('type-wish');

        if (action === 'add') {
            wishlist.addElement(model, itemId, typeWish, url, self);
        } else if (action === 'remove') {
            wishlist.removeElement(model, itemId, typeWish, url, self);
        }
    });

});
