/*
Scripts for admin part
*/
//use notation jquery noConflict
jQuery(document).ready(function($) {
    //delete one post
    $("a.slab-favorites-del").click(function (e) {
        if(!confirm("Подтвердите удаление")) return false;
        e.preventDefault(); //use for deny default redirect after link click
        var post = $(this).data('post'),
            parent = $(this).parent(),
            loader = parent.next(),
            li = $(this).closest('li');
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: 'slab_del', // part of name function which add post to DB
                security: slabFavorites.nonce,
                postId: post
            },
            beforeSend: function () {
                parent.fadeOut(300, function () {
                    loader.fadeIn();
                });
            },
            success: function (result) {
                loader.fadeOut(300, function () {
                    li.html("Удалено");
                });
            },
            error: function () {
                console.log("Ошибка запроса!");
            }
        });
    });

//delete all posts
    $("#slab-favorite-del-all").click(function(e) {
        if(!confirm("Подтвердите удаление")) return false;
        e.preventDefault();
        var $this = $(this),
            loader = $this.next(),
            parent = $this.parent(),
            list = parent.prev();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                security: slabFavorites.nonce,
                action: 'slab_del_all'
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
