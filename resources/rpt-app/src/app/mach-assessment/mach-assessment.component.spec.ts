import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MachAssessmentComponent } from './mach-assessment.component';

describe('MachAssessmentComponent', () => {
  let component: MachAssessmentComponent;
  let fixture: ComponentFixture<MachAssessmentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MachAssessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MachAssessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
