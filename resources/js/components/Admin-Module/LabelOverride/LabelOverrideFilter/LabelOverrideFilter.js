import React from 'react';
import IconButton from '@material-ui/core/IconButton';
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';
import MoreVertIcon from '@material-ui/icons/MoreVert';

const options = [
  {id:0, title:"All"},
  {id:1, title:"Product Title"},
  {id:2, title:"Sub Category"},
  {id:3, title:"Category"},
  {id:4, title:"Brand"},
];

const ITEM_HEIGHT = 48;

export default function LabelOverrideFilter(props) {
  const [anchorEl, setAnchorEl] = React.useState(null);
  const open = Boolean(anchorEl);

  const handleClick = (event) => {
    setAnchorEl(event.currentTarget);
  };

  const handleClose = (e) => {
      let colId = $(e.target).attr("col-id")
      let colToShow = null;
      let selectColName = "";
      switch (colId) {
          case "1":
          colToShow = [0,1];
          selectColName = "ASIN";
              break;
          case "2":
          colToShow = [2]
          selectColName = "subcategory_id";
              break;
          case "3":
          colToShow = [3]
          selectColName = "category_id";
              break;
          case "4":
          colToShow = [4]
          selectColName = "fk_account_id";
              break;
      
          default:
          colToShow = [0,1,2,3,4];
          selectColName = "all";
              break;
      }
      
      props.handleOnFilterSelect(colToShow, selectColName);
    setAnchorEl(null);
  };

  return (
    <>
      <IconButton
        aria-label="more"
        aria-controls="long-menu"
        aria-haspopup="true"
        style={{padding:"5px"}}
        onClick={handleClick}
      >
        <MoreVertIcon />
      </IconButton>
      <Menu
        id="long-menu"
        anchorEl={anchorEl}
        keepMounted
        open={open}
        onClose={handleClose}
        PaperProps={{
          style: {
            maxHeight: ITEM_HEIGHT * 4.5,
            width: '20ch',
          },
        }}
      >
        {options.map((option) => (
          <MenuItem key={option.id} col-id={option.id} selected={option.id === 0} onClick={handleClose}>
            {option.title}
          </MenuItem>
        ))}
      </Menu>
    </>
  );
}
