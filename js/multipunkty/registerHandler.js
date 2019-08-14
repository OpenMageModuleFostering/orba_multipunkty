/*
 * Function to handle fetching data for register button
 * 
 * @author Michał Zabielski <michal.zabielski@orba.pl>
 * 
 * @created 3-12-2013
 */

function registerStore()
{
    new Ajax.Request('/your/url', {
        onSuccess: function(response) {
          var data = Object.toJSON(response);
          
          $MP.api.connectPartner(data.appId, data.partnerFK, data.secret, data.eventUrl, data.returnUrl, {
              name: data.storeName,
              categories: '[ ' +data.categories+' ]',
              url: data.storeUrl,
              email: data.email,
              phone: data.phone,
              firstName: data.firstName
          });
        }
    });
}


