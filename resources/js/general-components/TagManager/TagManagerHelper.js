export const getNewDataOnTagDelete = (originalData, tagId) => {
    return originalData.map((data)=> {
        let newData = {...data};
        for (let index = 0; index < newData.tag.length; index++)
            if(newData.tag[index].fkTagId == tagId) newData.tag.splice(index,1);
        return newData;
    })
}
export const getNewDataOnTagEdit = (originalData, tagId, tag) => {
    return originalData.map((data)=> {
        let newData = {...data};
        for (let index = 0; index < newData.tag.length; index++)
            if(newData.tag[index].fkTagId == tagId) newData.tag[index].tag = tag;
        return newData;
    })
}
export const getNewDataOnTagAssign = (originalData, {asins, tagsObj, type}, moduleType, key) => {
    let asinArray = convertAsinObjectToArray(asins); 
    let tagsObjArray = []; 
    let tagsIdArray = []; 
    $.each(tagsObj, function (tagId, tag ) { 
        tagsObjArray.push(tag);
        tagsIdArray.push(parseInt(tagId));
    });
    return originalData.map((data)=> {
        let newData = {...data};
        if(asinArray.includes(newData[key])){
            if(newData.tag.length > 0){
                let tempTags = [];
                for (let index = 0; index < newData.tag.length; index++){
                    let isTagInTheSelectedTagList = tagsObjArray.includes(newData.tag[index].tag);//#tag1
                    if( !isTagInTheSelectedTagList ) {
                        tempTags.push(newData.tag[index]);
                        continue;
                    }
                    if(moduleType != 1){
                        let isTagTypeSameAsSelectedTagType = newData.tag[index].type == parseInt(type)//2
                        if(!isTagTypeSameAsSelectedTagType)
                            tempTags.push(newData.tag[index]);
                    }
                        
                }    
                tempTags.push(...getTagsObject(newData, tagsObjArray, tagsIdArray, moduleType, key, type));
                newData.tag = tempTags;
            }
            else{
                newData.tag = getTagsObject(newData, tagsObjArray, tagsIdArray, moduleType, key, type);
            }
        }
        return newData;
    })
}
export const getNewDataOnTagBulkUnAssignment = (originalData, asins, key)=>{
    let asinArray = convertAsinObjectToArray(asins); 
    return originalData.map((data)=> {
        let newData = {...data};
        if(asinArray.includes(newData[key])){
            if(newData.tag.length > 0)
            newData.tag = [];    
        }
        return newData;
    })
}
const getTagsObject = (data, tagsObj, tagsIdArray, moduleType, key, type) => {
    return tagsObj.map((tag, index)=> (moduleType == 1 ? {[key]: data[key], fkTagId: tagsIdArray[index], tag: tag} : {[key]: data[key], fkAccountId:data.fkAccountId, fkTagId: tagsIdArray[index], tag: tag, type:parseInt(type)}) );
}
const convertAsinObjectToArray = (asinObject) =>{
    let asinArray = []; 
    $.each(asinObject, function (asin, valueOfElement) { 
        asinArray.push(asin);
    });
    return asinArray;
}