export const GET_PRODUCT_TABLE_DATA = baseUrl+"/client/productList";
export const GET_ALL_TAGS_FOR_FILTER = baseUrl+"/client/tags/filter";
export const UN_ASSIGN_SINGLE_TAG = baseUrl+"/client/tags/singleUnAssign";

export function unAssignSingleTag(params,cb, ecb){
    // let url = params.type
    axios.post(
        UN_ASSIGN_SINGLE_TAG,
        {
            asin: params.asin,
            accountId: params.accountId,
            tagId: params.tagId,
        }
    ).then((response)=>{
        if (response.data.status) {
            cb(response.data);
        }else{
            ecb("Error");
        }
    }).catch((error)=>{
        ecb(error)
    });

}


export function getTagsForFilter(cb, ecb){
    axios.get(
        GET_ALL_TAGS_FOR_FILTER,
    ).then((response)=>{
        if (response.data.status) {
            cb(response.data);
        }else{
            ecb("Error");
        }
    }).catch((error)=>{
        ecb(error)
    });
}