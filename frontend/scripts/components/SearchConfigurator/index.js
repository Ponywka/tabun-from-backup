import React from 'react';
import {map} from 'lodash';
import classNames from 'classnames';
import {search as searchRoute} from 'lib/routes';
import {gettext as _} from 'core/lang';
import SearhParams from './logic.js';

export class NamedRadioGroup extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            selected: props.selected
        };
        this.change = this.change.bind(this);
    }

    change(event) {
        const val = event.target.value;
        event.target.blur();
        this.setState({selected: val});
        this.props.onChange(val);
    }

    render() {
        const buttons = map(this.props.buttons, (label, name) => {
            const className = classNames("btn btn-default", {"active": this.state.selected == name});
            return <button
                type="button"
                className={className}
                key={name}
                value={name}
                onClick={this.change}
            >{label}</button>;
        });

        return (
            <div className="btn-group btn-group-sm input-group" role="group">
                <div className="input-group-addon">{this.props.groupName}</div>
                {buttons}
            </div>
        );
    }
}

export class NamedDropdown extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            opened: false,
            selected: props.selected
        };
        this.toggle = this.toggle.bind(this);
        this.change = this.change.bind(this);
    }

    change(event) {
        const val = event.target.name;
        this.setState({
            opened: false,
            selected: val
        });
        this.props.onChange(val);
    }

    toggle() {
        this.setState({opened: !this.state.opened})
    }

    render() {
        const choices = map(this.props.choices, (label, name) => {
            const className = classNames({"active": this.state.selected == name});
            return (
                <li className={className} key={name} onClick={this.change}>
                    <a name={name}>{label}</a>
                </li>
            );
        });

        return (
            <div className="input-group-btn">
                <div
                    className={classNames("overlay", {"h-hidden": !this.state.opened})}
                    onClick={this.hide}
                ></div>
                <div className={classNames("btn-group", {"open": this.state.opened})}>
                    <button
                        className="btn btn-default dropdown-toggle"
                        type="button"
                        onClick={this.toggle}
                    >
                        <span>{this.props.groupName}&nbsp;{this.props.choices[this.state.selected]}&nbsp;</span>
                        <span className="caret"/>
                    </button>
                    <ul className="dropdown-menu" role="menu">
                        {choices}
                    </ul>
                </div>
            </div>
        );
    }
}

export default class SearchConfigurator extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            params: new SearhParams(props.coded),
            query: props.query || ""
        };
        this.handleQueryInput = this.handleQueryInput.bind(this);
        this.handleQueryType = this.handleQueryType.bind(this);
        this.handleSortDir = this.handleSortDir.bind(this);
        this.handleSortType = this.handleSortType.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleQueryInput (event) {
        // in-time validation/tooltips/help goes here
        this.setState({query: event.target.value});
    }

    handleQueryType (value) {
        const newParams = this.state.params;
        newParams.queryType = value;
        this.setState({params: newParams});
    }

    handleSortDir (value) {
        const newParams = this.state.params;
        newParams.sortDir = value;
        this.setState({params: newParams});
    }

    handleSortType (value) {
        const newParams = this.state.params;
        newParams.sortType = value;
        this.setState({params: newParams});
    }

    handleSubmit () {
        const params = {
            q: this.state.query,
            v: this.props.version,
            с: this.state.params.toString()
        };
        const query = Object
            .keys(params)
            .map(k => `${encodeURIComponent(k)}=${encodeURIComponent(params[k])}`)
            .join('&');
        document.location.replace(`${searchRoute}?${query}`);
    }

    render() {
        return (
            <div className="bs row">
                <div className="form-group col-lg-12">
                    <div className="input-group">
                        <NamedDropdown
                            groupName="Искать в"
                            choices={{topic: "топиках", comments: "комментариях"}}
                            selected={this.state.params.queryType}
                            onChange={this.handleQueryType}
                        />
                        <input
                            type="search"
                            className="form-control"
                            placeholder="Что ищем?"
                            value={this.state.query}
                            onChange={this.handleQueryInput}
                        />
                        <div className="input-group-btn">
                            <button
                                className="btn btn-primary btn-block"
                                onClick={this.handleSubmit}
                                disabled={this.state.query.length === 0}
                            >Искать!</button>
                        </div>
                    </div>
                </div>
                <div className="form-group col-lg-6">
                    <NamedRadioGroup
                        groupName="Сортировать по:"
                        buttons={{date: "дате", score: "релевантности", rating: "рейтингу"}}
                        selected={this.state.params.sortType}
                        onChange={this.handleSortType}
                    />
                </div>
                <div className="form-group col-lg-6">
                    <NamedRadioGroup
                        groupName="Упорядочить по:"
                        buttons={{asc: "возрастанию", desc: "убыванию"}}
                        selected={this.state.params.sortDir}
                        onChange={this.handleSortDir}
                    />
                </div>
            </div>
        )
    }
}
