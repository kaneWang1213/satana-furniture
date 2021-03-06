import { Injectable }     from '@angular/core';
import { Http, Response } from '@angular/http';
import { Observable }     from 'rxjs/Observable';
@Injectable()
export class HttpClientService {
  constructor (private http: Http) {}

  myJson = HttpClientService.get("jsonDataBase.php?selectProducts=0");

  get(url: string, optionHeader?: {[header: string]: string}): Observable<Response> {
    let headers = new Headers();
    // set option header
    for(let header in optionHeader) {
      let value = optionHeader[header];
      headers.append(header, value);
    }
    return this.http.get(url, {headers: headers,})
                    .map(this.extractData)
                    .catch(this.handleError);
  }
  private extractData(res: Response) {
    let body = res.json();
    return body.data || { };
  }
  private handleError (error: Response | any) {
    // In a real world app, we might use a remote logging infrastructure
    let errMsg: string;
    if (error instanceof Response) {
      const body = error.json() || '';
      const err = body.error || JSON.stringify(body);
      errMsg = `${error.status} - ${error.statusText || ''} ${err}`;
    } else {
      errMsg = error.message ? error.message : error.toString();
    }
    console.error(errMsg);
    return Observable.throw(errMsg);
  }
}