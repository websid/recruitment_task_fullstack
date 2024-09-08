import React, {Component} from 'react';
import axios from 'axios';

class ExchangeRates extends Component {
    constructor() {
        super();
        this.state = {
            currencyRates: {},
            loading: true,
            error: false,
        };
    }

    getDate = () => {
        const match = window.location.pathname.match(/([0-9]{4}-[0-9]{2}-[0-9]{2})/)

        if (match && match[1]) {
            return match[1]
        }

        return this.currentDate()
    }

    currentDate() {
        return new Date().toISOString().split('T')[0]
    }

    getRowClass(rate) {
        if (rate.mid > rate.today_mid) {
            return {backgroundColor: '#8ef990'}
        }
        if (rate.mid < rate.today_mid) {
            return {backgroundColor: '#f77b7b'}
        }
        return {backgroundColor: '#8c96f2'}
    }

    formatNumber(number) {
        return new Intl.NumberFormat('pl-PL', { maximumSignificantDigits: 8 }).format(
            number,
        )
    }

    componentDidMount() {
        this.getRates();
    }

    handleDateChange = (e) => {
        const date = new Date(e.target.value);
        if (date.getDay() === 0 || date.getDay() === 6) {
            this.setState({error: 'Weekends are not allowed.', currencyRates: []})
            return;
        }
        window.history.pushState(undefined, undefined, '/exchange-rates/' + e.target.value)
        this.getRates()
    }

    getRates = () => {
        this.setState({ currencyRates: [], loading: false, error: false });

        axios.get('/api/exchange-rates?date=' + this.getDate()).then(response => {
            if (response?.error) {
                this.setState({error: response.error});
            }
            this.setState({ currencyRates: response.data});
        }).catch(error =>  {
            alert(error.response.data.error)
        })
    }

    render() {
        const loading = this.state.loading;
        return(
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row mt-5">
                            <div className="col-md-4 offset-md-8">
                                <label htmlFor="date">Date</label>&nbsp;
                                <input type="date" id="date" min="2023-01-01"
                                       max={this.currentDate()}
                                       defaultValue={this.getDate()}
                                       onChange={this.handleDateChange}/>
                            </div>
                        </div>
                        <div className="row mt-5">
                            <div className="col-md-10 offset-md-1">
                                {this.state.error ?? (
                                    <div className="alert alert-danger" role="alert">{this.state.error}</div>
                                )}
                                <h2 className="text-center">Exchange rates</h2>
                                {loading ? (
                                    <div className={'text-center'}>
                                        <span className="fa fa-spin fa-spinner fa-4x"></span>
                                    </div>
                                ) : (
                                    <table className="table table-stripped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th rowSpan={2} align="center" style={{verticalAlign: "middle"}}>Currency</th>
                                            <th rowSpan={2} align="center" style={{verticalAlign: "middle"}}>Code</th>
                                            <th colSpan={3} align="center">{this.getDate()}</th>
                                            <th colSpan={3} align="center">today</th>
                                        </tr>
                                        <tr>
                                            <th>NBP</th>
                                            <th>Buy</th>
                                            <th>Sell</th>
                                            <th>NBP</th>
                                            <th>Buy</th>
                                            <th>Sell</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {this.state.currencyRates.map((rate, index) => (
                                            <tr key={index} style={this.getRowClass(rate)}>
                                                <td>{rate.currency}</td>
                                                <td>{rate.code}</td>
                                                <td align="right">{this.formatNumber(rate.mid)}</td>
                                                <td align="right">{this.formatNumber(rate.buy)}</td>
                                                <td align="right">{this.formatNumber(rate.sell)}</td>
                                                <td align="right">{this.formatNumber(rate.today_mid)}</td>
                                                <td align="right">{this.formatNumber(rate.today_buy)}</td>
                                                <td align="right">{this.formatNumber(rate.today_sell)}</td>
                                            </tr>
                                        ))}
                                        </tbody>
                                    </table>
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        )
    }
}

export default ExchangeRates;
