export const CAMPAIGN_FETCH_TAG_URL = window.baseUrl+ "/client/campaign/strategy/tags";
export const PRODUCT_TABLE_FETCH_TAG_URL = window.baseUrl+ "/client/tags";

export const CAMPAIGN_ADD_TAG_URL = window.baseUrl+ "/client/campaign/strategy/tags/add";
export const PRODUCT_TABLE_ADD_TAG_URL = window.baseUrl+ "/client/tags/add";

export const CAMPAIGN_BULK_DELETE_URL = window.baseUrl+ "/client/campaign/strategy/tags/getAllTagsToDelete";
export const PRODUCT_TABLE_BULK_DELETE_URL = window.baseUrl+ "/client/tags/getAllTagsToDelete";

export const CAMPAIGN_ASSIGN_TAG_URL = window.baseUrl+ "/client/campaign/strategy/tags/asign";
export const PRODUCT_TABLE_ASSIGN_TAG_URL = window.baseUrl+ "/client/tags/asign";



export function getTags(params,cb,ecb){
  let url = params.type == "1" ? PRODUCT_TABLE_FETCH_TAG_URL : CAMPAIGN_FETCH_TAG_URL;
    $.ajax({
        type: "GET",
        url: url,
        success: function(response) {
          if (response.status) {
            cb(response);
          } else {
            ecb(response);
          }
        }, //end success
        error: function(error) {
            ecb(error);
        }
      });

}
export function addTags(params,cb,ecb){
    let url = params.type == "1"? PRODUCT_TABLE_ADD_TAG_URL: CAMPAIGN_ADD_TAG_URL;
    axios.post(
        url,
        {
          tag: params.tag
        }
    ).then((response)=>{
        if(response.data.status){
            cb(response.data);
        }else{
            ecb(response.data.message);    
        }
    }).catch((error)=>{
        if(error.response)
        ecb(error.response.data.message)
        else{
            console.log(error)
            ecb("Some thing went worng in front end app, See Console")
        }
    });
}

export function updateTag (params,cb,ecb){
  const url = params.type == "1" ? baseUrl + "/client/tags/" + params.tagId + "/edit" :"/client/campaign/strategy/" +
  params.tagId + "/edit";
    $.ajax({
        type: "GET",
        url:url,
        data: {
          tagId: params.tagId,
          tagName: params.newTag
        },
        success: function(response) {
          if (response.status) {
            cb(response);
          } else {
            ecb({responseText:response.message,response})
          }
        }, //end success
        error: function(error) {
            ecb(error)
        }
      });
}

export function deleteTag (params,cb,ecb){
    const url = params.type == "1" ? baseUrl + "/client/tags/" + params.tagId + "/delete" : "/client/campaign/strategy/tags/" + params.tagId +"/delete";
    $.ajax({
        type: "GET",
        url: url,
        success: function(response) {
          if (response.status) {
            cb(response);
          } else {
            ecb(response)
          }
         
        }, //end success
        error: function(error) {
            ecb(error)
        }
      });
}


export function removeTagsInBulk(params,cb,ecb){
    let url = params.type=="1"? PRODUCT_TABLE_BULK_DELETE_URL: CAMPAIGN_BULK_DELETE_URL;
   
    $.ajax({
        type: "GET",
        url: url,
        data: {
          asins: params.selectedObject
        },
        success: function(response) {
            if (response.status) {
                cb(response);
            } else {
                ecb(response)
            }    
        }, //end success
        error: function(error) {
            ecb(error)
        }
      });
}

export function assingTag(params,cb,ecb){
    let url = params.type == "1"? PRODUCT_TABLE_ASSIGN_TAG_URL: CAMPAIGN_ASSIGN_TAG_URL;
   
    $.ajax({
        type: "POST",
        url: url,
        data: params.ajaxData,
        success: function(response) {
            if (response.status) {
                cb(response);
            } else {
                ecb(response);
            }   
        }, //end success
        error: function(error) {
            ecb(error);
        }
      });
}