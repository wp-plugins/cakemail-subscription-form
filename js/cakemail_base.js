CakeMail = { };

CakeMail.call_server = function( action, params, callback ){
    params = params || { };
    callback = callback || function(){};

    params['action'] = action;

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: params,
        success: callback,
        error: function(a,b,c,d){
            alert('Error calling server');
        },
    });
};
