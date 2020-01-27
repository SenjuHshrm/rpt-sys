import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FaasRecComponent } from './faas-rec.component';

describe('FaasRecComponent', () => {
  let component: FaasRecComponent;
  let fixture: ComponentFixture<FaasRecComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FaasRecComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FaasRecComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
