/*
Scripts for user part
*/
//use notation jquery noConflict
jQuery(document).ready(function($){
    $(".slab-favorite-link a").click(function(e) {
       e.preventDefault(); //use for deny default redirect after link click
        var whatDo = $(this).data('action');
        if(whatDo === "add") {
            $.ajax({
                type: "POST",
                url: slabFavorites.url,
                data:  {
                    action: 'slab_send', // part of name function which add post to DB
                    security: slabFavorites.nonce,
                    postId: slabFavorites.postId,
                    what_do: whatDo
                },
                beforeSend: function(){
                    $(".slab-favorite-link a").fadeOut(300, function(){
                        $(".slab-favorite-link .slab-favorite-hidden").fadeIn();
                    });
                },
                success: function(result) {
                    $(".slab-favorite-link .slab-favorite-hidden").fadeOut(300, function(){
                        $(".slab-favorite-link").html("Добавлено в избранное");
                        $(".widget_slab-favorites-widget ul").prepend(result);
                    });

                },
                error: function() {
                    console.log("Ошибка запроса!");
                }
            });
        } else {
            $.ajax({
                type: "POST",
                url: slabFavorites.url,
                data: {
                    action: 'slab_send', // part of name function which add post to DB
                    security: slabFavorites.nonce,
                    postId: slabFavorites.postId,
                    what_do: whatDo
                },
                beforeSend: function () {
                    $(".slab-favorite-link .slab-favorite-link a").fadeOut(300, function () {
                        $(".slab-favorite-link .slab-favorite-hidden").fadeIn();
                    });
                },
                success: function (result) {
                    $(".slab-favorite-link .slab-favorite-hidden").fadeOut(300, function () {
                        $(".slab-favorite-link").html("Удалено из избранного");
                        $(".widget_slab-favorites-widget").find("li.cat-item-" + slabFavorites.postId).remove();
                    });
                    //console.log(result);
                },
                error: function () {
                    console.log("Ошибка запроса!");
                }
            });
        }
    });

    $(".slab-favorites-del").click(function(e){
        e.preventDefault();
        var whatDo = $(this).data('action'),
            post = $(this).data('post');
        $.ajax({
            type: "POST",
            url: slabFavorites.url,
            data: {
                action: 'slab_send', // part of name function which add post to DB
                security: slabFavorites.nonce,
                postId: post,
                what_do: whatDo
            },
            beforeSend: function () {
                $(".cat-item-" + post).fadeOut(300, function () {
                    $(".cat-item-" + post + ".slab-favorite-hidden").fadeIn();
                });
            },
            success: function (result) {
                $(".cat-item-" + post + ".slab-favorite-hidden").fadeOut(300, function () {
                    $(".cat-item-" + post).html("Удалено из избранного");
                    $(".widget_slab-favorites-widget").find("li.cat-item-" + post).remove();
                });
                $("p.slab-favorite-link a").data("action", "add").text("В избранное");
                //console.log(result);
            },
            error: function () {
                console.log("Ошибка запроса!");
            }
        });
    });


    $("#slab-favorite-del-all").click(function(e) {
        e.preventDefault();
        var $this = $(this),
            loader = $this.next(),
            parent = $this.parent(),
            list = parent.prev();
        $.ajax({
            type: 'POST',
            url: slabFavorites.url,
            data: {
                security: slabFavorites.nonce,
                action: 'slab_del_all_from_widget'
            },
            beforeSend: function() {
                $this.fadeOut(300, function(){
                    loader.fadeIn();
                });
            },
            success: function(result){
                loader.fadeOut(300, function(){
                    if(result == "Записи удалены") {
                        parent.html(result);
                        list.fadeOut();
                    } else {
                        $this.fadeIn();
                        alert(result);
                    }

                });
            },
            error: function() {
                alert('Ошибка');
            }
        })
    });
});