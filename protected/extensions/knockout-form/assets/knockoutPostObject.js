function knockoutPostObject(viewModel, id, action, fn)
{
    viewModel.errors.removeAll();
    viewModel.errorStr.removeAll();
    //if(window.FormData)
    if (false)
    {
    }
    else
    {
        var json = {};

        for (i = 0; i < viewModel.attributes.length; i++)
        {
            key = viewModel.attributes[i];
            json[key] = viewModel[key]();
        }

        var post =
        {
            ajax:id,
            json:json,
            fd:0
        };
        post[viewModel.csrfName] = viewModel.csrfValue;

        var d = $.Deferred();
        $.post(action, post).done(function (p)
        {
            d.resolve(p);
        }).fail(function(data)
            {
                viewModel.disableSubmitButton(false);
            }, d.reject);

        return d.promise();
    }
}

function knockoutResponse(json, viewModel, fn)
{
    viewModel.disableSubmitButton(false);
    viewModel.errors.removeAll();
    if (typeof(json) == 'string') json = jQuery.parseJSON(json);
    if(json == null)
    {
        return;
    }
    if (json.HasValidationErrors)
    {
        $.each(json.error, function (id, msg)
        {
            viewModel.errors.push({id:id, message:msg});
            viewModel.errorStr.push(msg);
        });
    }
    else fn(json);
}