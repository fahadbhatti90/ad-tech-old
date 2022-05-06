import React from 'react';
import IconButton from '@material-ui/core/IconButton';
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import MoreVertIcon from '@material-ui/icons/MoreVert';
import DeleteIcon from '@material-ui/icons/Delete';
import Divider from '@material-ui/core/Divider';
import BlockIcon from '@material-ui/icons/Block';
import PauseIcon from '@material-ui/icons/Pause';
import PlayArrowIcon from '@material-ui/icons/PlayArrow';
// import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';

const ActionMenuItems=(props) => {
  const [anchorEl, setAnchorEl] = React.useState(null);

  const handleClick = event => {
    setAnchorEl(event.currentTarget);
  };

  const handleClose = () => {
    setAnchorEl(null);
  };

  const editRow = () => {
    if (props.onEditRow) {
        props.onEditRow(props.row);
    }
  };

  const deleteRow = () => {
    if (props.onDeleteRow) {
        props.onDeleteRow(props.row);
    }
  };

  const expired = props.row.isScheduleExpired;
  return (
    <div>
      <IconButton
        aria-label="more"
        aria-controls="long-menu"
        aria-haspopup="true"
        onClick={handleClick}
        size={props.size}
      >
        <MoreVertIcon className="btnHover"/>
      </IconButton>
      <Menu
        id="menu"
        getContentAnchorEl={null}
        anchorOrigin={{
          vertical: 'bottom',
          horizontal: 'center',
        }}
        transformOrigin={{
          vertical: 'top',
          horizontal: 'center',
        }}
        anchorEl={anchorEl}
        keepMounted
        open={Boolean(anchorEl)}
        onClose={handleClose}
      >

        <MenuItem>
            <ListItemIcon>
              <div className="flex flex-row space-x-1">
                <BlockIcon className="btnHover" fontSize="small"/> 
                <div>Stop</div>
              </div>
            </ListItemIcon>
        </MenuItem>
        
        <Divider />
        
        <MenuItem>
            <ListItemIcon>
                    {/* PlayArrowIcon */}
                <div className="flex flex-row space-x-1">
                  <PlayArrowIcon className="btnHover" fontSize="small"/>
                </div>
                <div>Play</div>
            </ListItemIcon>
        </MenuItem>
        
        <Divider />
        
        <MenuItem disabled={expired} onClick={editRow}>
          <ListItemIcon>
            <div className="flex flex-row space-x-1">
              <EditIcon className="btnHover" fontSize="small" />
              <div>Edit</div>
            </div>
          </ListItemIcon>
        </MenuItem>
        
        <Divider />
        
        <MenuItem disabled={expired} onClick={deleteRow}>
          <ListItemIcon>
            <div className="flex flex-row space-x-1">
              <DeleteIcon disabled={expired} fontSize="small" className="btnHover" />
              <div>Delete</div>
            </div>
          </ListItemIcon>
        </MenuItem>
      </Menu>
    </div>
  );
};
export default ActionMenuItems;