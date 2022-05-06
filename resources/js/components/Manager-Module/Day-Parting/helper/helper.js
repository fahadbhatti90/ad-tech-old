import * as Yup from "yup";
import {requiredValidationHelper} from "./../../../../helper/helper";

export function scheduleNameValidate() {
    Yup.object().shape({
        scheduleName: requiredValidationHelper()
            .matches(
                /^[a-zA-Z0-9:_-]+$/,
                "The name can only consist of alphabetical,number,underscore,hyphen and colon"
            )
    });
}