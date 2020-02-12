import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import config from '../default/config'

@Injectable({
	providedIn: 'root'
})

export class assessBldg {

  constructor(private http: HttpClient) { }

  private setReqHeaders() {
    return {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
  			'Authorization': 'Bearer ' + localStorage.getItem('auth')
      })
    }
  }

  public saveBldg(data: any): Observable<any> {
    return this.http.put(config.api + '/api/bldg-asmt/add', data, this.setReqHeaders())
  }

  public updateBldg(data: any): Observable<any> {
    return this.http.put(config.api + '/api/bldg-asmt/update', data, this.setReqHeaders())
  }

}
