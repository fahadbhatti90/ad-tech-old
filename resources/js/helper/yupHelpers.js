import * as Yup from 'yup';

export const SUPPORTED_FORMATS = [
        "xls",
        "xlsx", 
        "csv",
        "application/vnd.ms-excel", 
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", 
    ];
export function stringRequiredValidationHelper(name) {
    return Yup.string(capitalizeFirstLetter(name) + " must be a string").min(1, capitalizeFirstLetter(name) + " is required");
}
export function stringRequiredEmailValidationHelper(name) {
    return Yup.string(capitalizeFirstLetter(name) + " must be a string")
    .min(1, capitalizeFirstLetter(name) + " is required")
    .email();
}

export function stringMinLengthValidationHelper(name, length) {
    return Yup.string(capitalizeFirstLetter(name) + " must be a string").min(length, capitalizeFirstLetter(name) + " length must be greater than " + (length - 1));
}
export function stringMaxLengthValidationHelper(name, length) {
    return Yup.string(capitalizeFirstLetter(name) + " must be a string")
    .min(1, capitalizeFirstLetter(name) + " is required")
    .max(length, capitalizeFirstLetter(name) + " length must be less than " + (length));
}
export function stringMaxLengthAlphaNumericValidationHelper(name, length, regex = /^([a-zA-Z0-9 ]+)$/) {
    return Yup.string(capitalizeFirstLetter(name) + " must be a string")
    .min(1, capitalizeFirstLetter(name) + " is required")
    .max(length, capitalizeFirstLetter(name) + " length must be less than " + (length))
    .matches(regex, "Only alphanumeric characters are allowed");
}

export function arrayRequiredValidationHelper(name) {
    return Yup.array().min(1, capitalizeFirstLetter(name) + " is Required");
}

export function poitiveIntegerValidationHelper(name) {
    return Yup.number(name + " must be an integer number")
        .min(1, capitalizeFirstLetter(name) + ' is required')
        .integer(capitalizeFirstLetter(name) + " must be an integer")
        .moreThan(-1, capitalizeFirstLetter(name) + " must be a positive number");
}
export function numberValidationHelper(name) {
    return Yup.number(name + " must be a number")
        .min(1, capitalizeFirstLetter(name) + ' is required')
        .moreThan(-1, capitalizeFirstLetter(name) + " must be a positive number");
}

export function capitalizeFirstLetter(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

export function objectRequiredValidationHelper(name) {
    return Yup.mixed().nullable().test(
        'objectValidationError',
        capitalizeFirstLetter(name) + ' is required',
        value => value !== null,//sucess senario 
    );
}
export function isValidFileType(file){
    return SUPPORTED_FORMATS.includes(file.type);
}
export function uploadValidationHelper(name) {
    return Yup.mixed()
        .required(capitalizeFirstLetter(name) + ' is required')
        .test(
            'fileType', "Unsupported File Format",
            value => value != null && SUPPORTED_FORMATS.includes(value.type))
}
