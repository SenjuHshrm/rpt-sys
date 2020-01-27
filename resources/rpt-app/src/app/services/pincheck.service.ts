import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import config from '../default/config';
@Injectable({
  providedIn: 'root'
})

export class pincheck {
  constructor(private http: HttpClient) { }

  checkPin(data: any): Observable<any> {
    let headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + localStorage.getItem('auth')
    });
    let opt = { headers: headers };
    let count = (data.pin.split('-')).length
    let route = ''
    if(count == 5) {
      route = '/api/check-pin-land';
    } else if(count == 6) {
      route = '/api/check-pin-bldg'
    }
    return this.http.post(config.api + route, data, opt);
  }
}
