import React from "react";
import "./styles.scss";

class EmailChips extends React.Component {

    constructor(props){
        super(props)
        this.state = {
            items: [],
            value: "",
            error: null
        };
    }


    componentDidMount() {
        let items = this.props.items;
        this.setState({
            items
        })
    }
    static getDerivedStateFromProps(nextProps, prevState){

        if((nextProps.items && prevState.items.length <= 0) || nextProps.isReset) {
            return {
                items:nextProps.items
            }
        }
        return null;
    }
   
    handleKeyDown = evt => {
        if (["Enter", "Tab", ","].includes(evt.key)) {
            evt.preventDefault();
            var value = this.state.value.trim();
            if (value && this.isValid(value)) {
                this.setState({
                    items: [...this.state.items, this.state.value],
                    value: ""
                }, () => {
                    this.props.getUpdatedItems(this.state.items);
                });

            }
        }
    };

    handleChange = evt => {
        this.setState({
            value: evt.target.value,
            error: null
        });
    };

    handleDelete = item => {
        let newItems = this.state.items.filter(i => i !== item);
        if(newItems.length <=0){
            newItems = [];
        }
            this.props.getUpdatedItems([]);
        this.setState({
            items: this.state.items.filter(i => i !== item)
        }, () => {
            this.props.getUpdatedItems(this.state.items);
        });
    };

    handlePaste = evt => {
        evt.preventDefault();
        var paste = evt.clipboardData.getData("text");
        var emails = paste.match(/[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/g);

        if (emails) {
            var toBeAdded = emails.filter(email => !this.isInList(email));

            this.setState({
                items: [...this.state.items, ...toBeAdded]
            }, () => {
                this.props.getUpdatedItems(this.state.items);
            });
        }
    };

    isValid(email) {
        let error = null;

        if (this.isInList(email)) {
            error = `${email} has already been added.`;
        }

        if (!this.isEmail(email)) {
            error = `${email} is not a valid email address.`;
        }

        if (error) {
            this.setState({error});

            return false;
        }

        return true;
    }

    isInList(email) {
        return this.state.items.includes(email);
    }

    isEmail(email) {
        return /[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/.test(email);
    }

    render() {
        return (
            <>
                <div className="wrapper">
                    <div className="parentItem">
                        <div className={this.props.classListItem?this.props.classListItem:"listItems"}>
                            {this.state.items.map(item => (
                                <div className="tag-item" key={item}>
                                    {item}
                                    <button
                                        type="button"
                                        className="button"
                                        onClick={() => this.handleDelete(item)}
                                    >
                                        &times;
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                    <input
                        className={"input " + (this.state.error && " has-error")}
                        value={this.state.value}
                        placeholder="Type or paste and press `Enter`..."
                        onKeyDown={this.handleKeyDown}
                        onChange={this.handleChange}
                        onPaste={this.handlePaste}
                    />

                    {this.state.error && <p className="error">{this.state.error}</p>}
                </div>
            </>
        );
    }
}

export default EmailChips;