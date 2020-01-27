import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LandReassessmentComponent } from './land-reassessment.component';

describe('LandReassessmentComponent', () => {
  let component: LandReassessmentComponent;
  let fixture: ComponentFixture<LandReassessmentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LandReassessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LandReassessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
