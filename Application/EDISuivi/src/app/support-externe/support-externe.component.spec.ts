import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SupportExterneComponent } from './support-externe.component';

describe('SupportExterneComponent', () => {
  let component: SupportExterneComponent;
  let fixture: ComponentFixture<SupportExterneComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SupportExterneComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SupportExterneComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
