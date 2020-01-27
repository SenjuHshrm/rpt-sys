import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LandAssessmentComponent } from './land-assessment.component';

describe('LandAssessmentComponent', () => {
  let component: LandAssessmentComponent;
  let fixture: ComponentFixture<LandAssessmentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LandAssessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LandAssessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
