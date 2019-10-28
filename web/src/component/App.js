import React, { Component } from 'react';
import http from '../services/http.services';

class App extends Component {

  constructor(props) {
    super(props);
    this.state = {
      lts: [{ 'id_lts': 0, 'nombre': '' }],
      franquicias: [],
      objet: [{
        'presupuesto': 0,
        'id_franquicia': 0,
        'nombre': 0,
        'venta': 0
      }],
      costos: [],
      ventas: []
    }
  }

  componentDidMount() {
    http._GET("tienda/tienda.php").then(res => {
      var _lts = this.state.lts;
      this.setState({
        costos: res.costos,
        ventas: res.ventas,
        lts: _lts.concat(res.lts),
        franquicias: res.franquicias
      });
    }).catch(err => {
      console.log(err.toString());
    })
  }

  render() {
    const { lts, franquicias } = this.state;
    return (
      <div>
        <table className="table table-striped">
          <tbody>
            {lts.map((lt, i) => {
              return (
                <tr key={i}>
                  <td>{lt.nombre}</td>
                  {i == 0 ?
                    franquicias.map((frq) => {
                      return (
                        Object.keys(frq).map((keyName, k) => {
                          return (
                            keyName != 'id_franquicia' &&
                            <td key={k}>{frq[keyName]}</td>
                          )
                        })
                      )
                    })
                    :
                    franquicias.map((frq) => {
                      return (
                        Object.keys(frq).map((keyName, k) => {
                          return (
                            keyName != 'id_franquicia' &&
                            <td key={k} id="_td">
                              {keyName == 'nombre' ?
                                this.handleGetCosto(lt.id_lt, frq.id_franquicia)
                                : (keyName == 'venta' ? this.handleGetVenta(lt.id_lt, frq.id_franquicia) : 0)}
                            </td>
                          )
                        })
                      )
                    })
                  }
                </tr>
              )
            })}
          </tbody>
        </table>
      </div>
    )
  }

  handleGetCosto(id_lt, id_franquicia) {
    const { costos } = this.state;
    var _costo = costos.filter(item => {
      if (id_lt == item.id_lt && id_franquicia == item.id_franquicia) {
        return item;
      }
    });

    if (_costo.length > 0) {
      return _costo[0].total_costo;
    }

    return 0;
  }

  handleGetVenta(id_lt, id_franquicia) {
    const { ventas } = this.state;
    var _venta = ventas.filter(item => {
      if (id_lt == item.id_lt && id_franquicia == item.id_franquicia) {
        return item;
      }
    });

    if (_venta.length > 0) {
      return _venta[0].venta;
    }

    return 0;
  }

}

export default App;
